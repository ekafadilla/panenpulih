<?php

namespace App\Services;

use App\Models\KebutuhanPembeli;
use App\Models\RekomendasiPembeli;
use App\Models\RencanaPanen;
use Carbon\Carbon;

class BuyerMatchingService
{
    /**
     * Hitung pencocokan calon pembeli dengan surplus rencana panen (Section 7.3)
     * Formula: M = 0.30*C + 0.20*Q + 0.20*L + 0.20*T + 0.10*J
     *
     * @return list<RekomendasiPembeli>
     */
    public function cocokanUntukRencanaPanen(RencanaPanen $rencana): array
    {
        $surplusKg = (float) $rencana->potensi_surplus_kg;

        // Ambil semua kebutuhan pembeli aktif
        $kebutuhanList = KebutuhanPembeli::with(['pembeli', 'komoditas'])
            ->where('status', 'aktif')
            ->get();

        $hasilRekomendasi = [];

        foreach ($kebutuhanList as $kebutuhan) {
            // C: Kesesuaian Komoditas (0 atau 100)
            $skorC = ($rencana->komoditas_id === $kebutuhan->komoditas_id) ? 100.0 : 0.0;

            // Jika komoditas sama sekali tidak sama, skip atau beri skor total 0
            if ($skorC <= 0.0) {
                continue;
            }

            // Q: Kesesuaian Kuantitas (0-100)
            $butuhKg = (float) $kebutuhan->jumlah_kg;
            $skorQ = $this->hitungSkorKuantitas($surplusKg, $butuhKg);

            // L: Kedekatan Lokasi (0-100) & Jarak (km)
            $jarakKm = $this->hitungJarakHaversine(
                (float) $rencana->latitude,
                (float) $rencana->longitude,
                (float) $kebutuhan->latitude,
                (float) $kebutuhan->longitude
            );
            $skorL = $this->hitungSkorLokasi($jarakKm);

            // T: Kesesuaian Waktu (0-100)
            $skorT = $this->hitungSkorWaktu(
                $rencana->tanggal_perkiraan_panen,
                $kebutuhan->tanggal_mulai_butuh,
                $kebutuhan->batas_tanggal_butuh
            );

            // J: Kesesuaian Kualitas / Karakteristik Grade (0-100)
            $skorJ = $this->hitungSkorKualitas($rencana->grade, $kebutuhan->grade_diterima);

            // Formula Utama: M = 0.30*C + 0.20*Q + 0.20*L + 0.20*T + 0.10*J
            $skorTotal = (0.30 * $skorC) +
                         (0.20 * $skorQ) +
                         (0.20 * $skorL) +
                         (0.20 * $skorT) +
                         (0.10 * $skorJ);

            $skorTotal = round(min(100.0, max(0.0, $skorTotal)), 2);

            // Alasan rekomendasi manusiawi
            $alasan = $this->susunAlasanRekomendasi($skorTotal, $jarakKm, $butuhKg, $surplusKg, $kebutuhan);

            $rekomendasi = RekomendasiPembeli::updateOrCreate(
                [
                    'rencana_panen_id' => $rencana->id,
                    'kebutuhan_pembeli_id' => $kebutuhan->id,
                ],
                [
                    'skor_total' => $skorTotal,
                    'skor_komoditas' => $skorC,
                    'skor_kuantitas' => round($skorQ, 2),
                    'skor_lokasi' => round($skorL, 2),
                    'skor_waktu' => round($skorT, 2),
                    'skor_kualitas' => round($skorJ, 2),
                    'jarak_km' => round($jarakKm, 1),
                    'alasan_rekomendasi' => $alasan,
                    'status' => 'rekomendasi',
                ]
            );

            $hasilRekomendasi[] = $rekomendasi;
        }

        // Urutkan dari skor tertinggi
        usort($hasilRekomendasi, fn ($a, $b) => $b->skor_total <=> $a->skor_total);

        return $hasilRekomendasi;
    }

    /**
     * Hitung skor kesesuaian kuantitas
     */
    private function hitungSkorKuantitas(float $surplusKg, float $butuhKg): float
    {
        if ($surplusKg <= 0 || $butuhKg <= 0) {
            return 10.0;
        }

        $min = min($surplusKg, $butuhKg);
        $max = max($surplusKg, $butuhKg);

        $rasio = ($min / $max) * 100.0;

        return min(100.0, max(15.0, $rasio));
    }

    /**
     * Haversine formula untuk menghitung jarak antara 2 titik koordinat (km)
     */
    public function hitungJarakHaversine(?float $lat1, ?float $lon1, ?float $lat2, ?float $lon2): float
    {
        if (! $lat1 || ! $lon1 || ! $lat2 || ! $lon2) {
            return 25.0; // Default estimasi jika koordinat belum diatur
        }

        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    private function hitungSkorLokasi(float $jarakKm): float
    {
        if ($jarakKm <= 10.0) {
            return 100.0; // Sangat dekat (< 10 km)
        }
        if ($jarakKm <= 25.0) {
            return 85.0;  // Satu kabupaten / tetangga
        }
        if ($jarakKm <= 50.0) {
            return 70.0;  // Radius menengah
        }
        if ($jarakKm <= 100.0) {
            return 50.0;  // Antar kota sekitar
        }
        if ($jarakKm <= 200.0) {
            return 30.0;
        }
        return 15.0;
    }

    private function hitungSkorWaktu($tglPanenRaw, $tglMulaiButuhRaw, $batasTglButuhRaw): float
    {
        $panen = Carbon::parse($tglPanenRaw);
        $mulai = Carbon::parse($tglMulaiButuhRaw);
        $akhir = Carbon::parse($batasTglButuhRaw);

        // Jika panen tepat pada rentang tanggal dibutuhkan
        if ($panen->betweenIncluded($mulai, $akhir)) {
            return 100.0;
        }

        // Jika panen 1-3 hari sebelum kebutuhan (sangat ideal untuk distribusi)
        $selisihHari = $panen->diffInDays($mulai, false);
        if ($selisihHari >= -3 && $selisihHari <= 0) {
            return 95.0;
        }

        if (abs($selisihHari) <= 7) {
            return 65.0;
        }

        if (abs($selisihHari) <= 14) {
            return 40.0;
        }

        return 20.0;
    }

    private function hitungSkorKualitas(?string $gradePetani, ?string $gradePembeli): float
    {
        if (! $gradePembeli || $gradePembeli === 'semua') {
            return 100.0;
        }

        if ($gradePetani === $gradePembeli) {
            return 100.0;
        }

        // Jika petani Grade A dan pembeli butuh Grade B atau Imperfect
        if ($gradePetani === 'grade_a' && in_array($gradePembeli, ['grade_b', 'imperfect'], true)) {
            return 90.0;
        }

        // Jika UMKM butuh imperfect (olahan pasta/saus) dan petani ada campur/imperfect
        if ($gradePembeli === 'imperfect' && in_array($gradePetani, ['campur', 'imperfect'], true)) {
            return 100.0;
        }

        if ($gradePetani === 'grade_b' && $gradePembeli === 'grade_a') {
            return 40.0;
        }

        return 50.0;
    }

    private function susunAlasanRekomendasi(float $skorTotal, float $jarakKm, float $butuhKg, float $surplusKg, KebutuhanPembeli $kebutuhan): string
    {
        $pembeli = $kebutuhan->pembeli;
        $namaUsaha = $pembeli->institution ?? $pembeli->name;
        $jenis = ucfirst(str_replace('_', ' ', $pembeli->pembeliProfile->jenis_usaha ?? 'Pembeli'));

        $jarakStr = "{$jarakKm} km";
        $persenSerap = round(min(100.0, ($butuhKg / max(1.0, $surplusKg)) * 100.0));

        if ($skorTotal >= 80.0) {
            return "Pilihan Sangat Tepat! {$namaUsaha} ({$jenis}) berjarak hanya {$jarakStr} dengan kebutuhan {$butuhKg} kg (dapat menyerap ~{$persenSerap}% surplus Anda) tepat di jendela waktu panen.";
        }
        if ($skorTotal >= 60.0) {
            return "Kecocokan Baik: {$namaUsaha} membutuhkan {$butuhKg} kg dengan toleransi grade fleksibel, jarak {$jarakStr}. Potensi kesepakatan tinggi.";
        }
        return "Alternatif Tambahan: Berjarak {$jarakStr}, membutuhkan {$butuhKg} kg untuk kebutuhan {$kebutuhan->tujuan_penggunaan}.";
    }
}
