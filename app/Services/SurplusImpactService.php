<?php

namespace App\Services;

use App\Models\AnalisisSurplus;
use App\Models\Komoditas;
use App\Models\Penyaluran;
use App\Models\RencanaPanen;

class SurplusImpactService
{
    /**
     * Hitung metrik dampak nasional / platform PANENPULIH (Section 5.1 & Section 11)
     *
     * @return array<string, mixed>
     */
    public function getMetrikPlatform(): array
    {
        // 1. Total potensi surplus teridentifikasi sebelum panen (kg)
        $totalSurplusTerdeteksiKg = (float) RencanaPanen::sum('potensi_surplus_kg');

        // 2. Total surplus pangan berhasil terserap & tersalurkan (kg)
        $totalTersalurkanKg = (float) Penyaluran::whereIn('status_verifikasi', ['terverifikasi', 'selesai'])
            ->sum('jumlah_realisasi_kg');

        // 3. Tingkat penyerapan surplus rata-rata platform (%)
        // Rumus: (jumlah berhasil disalurkan / jumlah potensi surplus) * 100%
        $tingkatPenyerapanPersen = $totalSurplusTerdeteksiKg > 0
            ? round(($totalTersalurkanKg / $totalSurplusTerdeteksiKg) * 100.0, 1)
            : 0.0;

        // 4. Nilai ekonomi pangan terselamatkan (Rp)
        $totalNilaiEkonomiRp = (float) Penyaluran::whereIn('status_verifikasi', ['terverifikasi', 'selesai'])
            ->sum('harga_total_realisasi');

        // 5. Metrik Dampak SDG 12 & SDG 2:
        // Tonase food loss dicegah
        $foodLossDicegahTon = round($totalTersalurkanKg / 1000.0, 2);

        // Estimasi porsi pangan terselamatkan (asumsi 1 porsi = 400g / 0.4 kg)
        $estimasiPorsiPangan = (int) round($totalTersalurkanKg / 0.4);

        // Estimasi emisi gas rumah kaca dicegah (FAO: ~1.9 kg CO2e per 1 kg food loss dicegah)
        $estimasiCo2eDicegahKg = round($totalTersalurkanKg * 1.9, 1);

        // Distribusi kategori risiko pre-harvest
        $distribusiRisiko = [
            'Rendah' => AnalisisSurplus::where('kategori_risiko', 'Rendah')->count(),
            'Sedang' => AnalisisSurplus::where('kategori_risiko', 'Sedang')->count(),
            'Tinggi' => AnalisisSurplus::where('kategori_risiko', 'Tinggi')->count(),
            'Sangat Tinggi' => AnalisisSurplus::where('kategori_risiko', 'Sangat Tinggi')->count(),
        ];

        return [
            'total_surplus_terdeteksi_kg' => $totalSurplusTerdeteksiKg,
            'total_tersalurkan_kg' => $totalTersalurkanKg,
            'tingkat_penyerapan_persen' => $tingkatPenyerapanPersen,
            'total_nilai_ekonomi_rp' => $totalNilaiEkonomiRp,
            'food_loss_dicegah_ton' => $foodLossDicegahTon,
            'estimasi_porsi_pangan' => $estimasiPorsiPangan,
            'estimasi_co2e_dicegah_kg' => $estimasiCo2eDicegahKg,
            'distribusi_risiko' => $distribusiRisiko,
        ];
    }

    /**
     * Hitung metrik dampak untuk petani spesifik
     *
     * @return array<string, mixed>
     */
    public function getMetrikPetani(int $petaniId): array
    {
        $rencanaIds = RencanaPanen::where('petani_id', $petaniId)->pluck('id');

        $totalSurplusKg = (float) RencanaPanen::where('petani_id', $petaniId)->sum('potensi_surplus_kg');

        $penyaluranQuery = Penyaluran::whereHas('penawaran', function ($q) use ($petaniId) {
            $q->where('petani_id', $petaniId);
        })->whereIn('status_verifikasi', ['terverifikasi', 'selesai']);

        $totalTerserapKg = (float) $penyaluranQuery->sum('jumlah_realisasi_kg');
        $totalPendapatanSurplusRp = (float) $penyaluranQuery->sum('harga_total_realisasi');

        $tingkatPenyerapan = $totalSurplusKg > 0
            ? round(($totalTerserapKg / $totalSurplusKg) * 100.0, 1)
            : 0.0;

        return [
            'total_surplus_kg' => $totalSurplusKg,
            'total_terserap_kg' => $totalTerserapKg,
            'total_pendapatan_rp' => $totalPendapatanSurplusRp,
            'tingkat_penyerapan' => $tingkatPenyerapan,
        ];
    }
}
