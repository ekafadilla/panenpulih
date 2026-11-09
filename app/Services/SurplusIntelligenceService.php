<?php

namespace App\Services;

use App\Models\AnalisisSurplus;
use App\Models\KebutuhanPembeli;
use App\Models\RencanaPanen;
use Carbon\Carbon;

class SurplusIntelligenceService
{
    /**
     * Hitung potensi surplus dan evaluasi indikator risiko surplus sebelum panen (Section 7.1 & 7.2)
     * Formula Surplus: S = H - P - K
     * Formula Risiko: R = 0.25*Sp + 0.20*W + 0.25*U + 0.15*G + 0.15*D
     */
    public function analisa(RencanaPanen $rencana): AnalisisSurplus
    {
        $h = (float) $rencana->estimasi_hasil_kg;
        $p = (float) $rencana->jumlah_terpesan_kg;
        $k = (float) $rencana->jumlah_alokasi_lain_kg;

        // 1. S = H - P - K
        $s = max(0.0, $h - $p - $k);

        // Update surplus pada model rencana panen
        $rencana->update(['potensi_surplus_kg' => $s]);

        // 2. Indikator Sp (Persentase Surplus terhadap total panen H)
        $persentaseSurplus = $h > 0 ? ($s / $h) * 100.0 : 0.0;
        $skorSp = min(100.0, $persentaseSurplus);

        // 3. Indikator W (Kedekatan Waktu Panen)
        $tglPanen = Carbon::parse($rencana->tanggal_perkiraan_panen);
        $hariMenujuPanen = (int) Carbon::now()->diffInDays($tglPanen, false);
        $skorW = $this->hitungSkorWaktu($hariMenujuPanen);

        // 4. Indikator U (Kerentanan Komoditas / Perishability)
        $komoditas = $rencana->komoditas;
        $skorU = $komoditas ? (float) $komoditas->skor_kerentanan : 70.0;

        // 5. Indikator G (Keterbatasan Penyimpanan)
        $skorG = $this->hitungSkorPenyimpanan($rencana->fasilitas_penyimpanan);

        // 6. Indikator D (Ketidakseimbangan Permintaan Pasar vs Surplus)
        $skorD = $this->hitungSkorPermintaan($rencana->komoditas_id, $s, $rencana->latitude, $rencana->longitude);

        // 7. Formula Risiko Terpadu: R = 0.25*Sp + 0.20*W + 0.25*U + 0.15*G + 0.15*D
        $skorRisikoTotal = (0.25 * $skorSp) +
                           (0.20 * $skorW) +
                           (0.25 * $skorU) +
                           (0.15 * $skorG) +
                           (0.15 * $skorD);

        $skorRisikoTotal = round(min(100.0, max(0.0, $skorRisikoTotal)), 2);

        // 8. Kategori Risiko
        $kategoriRisiko = $this->tentukanKategoriRisiko($skorRisikoTotal);

        // 9. Rekomendasi Mitigasi Cerdas
        $rekomendasi = $this->buatRekomendasiTindakan($kategoriRisiko, $hariMenujuPanen, $komoditas->nama ?? 'Komoditas', $s);

        // Simpan / update ke database
        return AnalisisSurplus::updateOrCreate(
            ['rencana_panen_id' => $rencana->id],
            [
                'potensi_surplus_kg' => round($s, 2),
                'persentase_surplus' => round($persentaseSurplus, 2),
                'skor_persentase_surplus' => round($skorSp, 2),
                'skor_waktu' => round($skorW, 2),
                'skor_kerentanan' => round($skorU, 2),
                'skor_penyimpanan' => round($skorG, 2),
                'skor_permintaan' => round($skorD, 2),
                'skor_risiko_total' => $skorRisikoTotal,
                'kategori_risiko' => $kategoriRisiko,
                'hari_menuju_panen' => $hariMenujuPanen,
                'rekomendasi_tindakan' => $rekomendasi,
                'catatan_analisis' => "Analisis otomatis pre-harvest surplus intelligence dihitung pada " . Carbon::now()->isoFormat('D MMMM Y, HH:mm'),
                'tanggal_analisis' => Carbon::now()->toDateString(),
            ]
        );
    }

    private function hitungSkorWaktu(int $hari): float
    {
        if ($hari <= 0) {
            return 100.0; // Panen sekarang / lewat
        }
        if ($hari <= 3) {
            return 95.0; // Sangat mendesak
        }
        if ($hari <= 7) {
            return 80.0; // Mendesak (< 1 minggu)
        }
        if ($hari <= 14) {
            return 60.0; // 1-2 minggu
        }
        if ($hari <= 21) {
            return 40.0; // 2-3 minggu
        }
        if ($hari <= 30) {
            return 25.0; // 1 bulan
        }
        return 10.0; // > 1 bulan (masih aman)
    }

    private function hitungSkorPenyimpanan(?string $fasilitas): float
    {
        return match ($fasilitas) {
            'none', 'open_air' => 95.0, // Tanpa penyimpanan / ruang terbuka (risiko sangat tinggi)
            'shaded_shed' => 65.0,      // Bangsal sederhana berteduh
            'ventilated_warehouse' => 35.0, // Gudang berventilasi baik
            'cold_storage' => 10.0,     // Cold storage pendingin (risiko rendah)
            default => 70.0,
        };
    }

    private function hitungSkorPermintaan(int $komoditasId, float $surplusKg, ?float $lat, ?float $lng): float
    {
        if ($surplusKg <= 0) {
            return 10.0;
        }

        // Hitung total kuantitas kebutuhan pembeli aktif untuk komoditas ini
        $totalPermintaan = (float) KebutuhanPembeli::where('komoditas_id', $komoditasId)
            ->where('status', 'aktif')
            ->sum('jumlah_kg');

        if ($totalPermintaan <= 0) {
            return 95.0; // Pasar sangat sepi di platform
        }

        $rasio = $totalPermintaan / $surplusKg;

        if ($rasio >= 2.0) {
            return 15.0; // Permintaan melimpah (pembeli antri)
        }
        if ($rasio >= 1.0) {
            return 35.0; // Permintaan seimbang
        }
        if ($rasio >= 0.5) {
            return 65.0; // Permintaan separuh dari surplus
        }
        return 85.0; // Surplus jauh melebihi permintaan terdaftar
    }

    public function tentukanKategoriRisiko(float $skor): string
    {
        if ($skor < 25.0) {
            return 'Rendah';
        }
        if ($skor < 50.0) {
            return 'Sedang';
        }
        if ($skor < 75.0) {
            return 'Tinggi';
        }
        return 'Sangat Tinggi';
    }

    private function buatRekomendasiTindakan(string $kategori, int $hari, string $namaKomoditas, float $surplusKg): string
    {
        if ($kategori === 'Sangat Tinggi' || $kategori === 'Tinggi') {
            return "URGENT: Potensi surplus {$surplusKg} kg {$namaKomoditas} berisiko tinggi busuk/terbuang. Disarankan segera menghubungi rekomendasi pedagang dan UMKM pengolah lokal (saus, keripik, pasta), pertimbangkan penyesuaian harga pre-order, dan siapkan kemasan berventilasi.";
        }
        if ($kategori === 'Sedang') {
            return "PERHATIAN: Surplus {$surplusKg} kg {$namaKomoditas} memiliki risiko moderat. Pantau konfirmasi calon pembeli dalam 48 jam ke depan dan manfaatkan penawaran langsung ke mitra pembeli di radius terdekat.";
        }
        return "TERKENDALI: Potensi surplus berada dalam batas aman. Alokasi pasar reguler mencukupi, pertahankan jadwal panen dan jaga kualitas produk.";
    }
}
