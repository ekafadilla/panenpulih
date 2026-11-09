<?php

namespace App\Http\Controllers;

use App\Models\Komoditas;
use App\Models\RencanaPanen;
use App\Services\SurplusImpactService;
use App\Services\SurplusIntelligenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(SurplusImpactService $impactService): View
    {
        $metrik = $impactService->getMetrikPlatform();

        $komoditasList = Komoditas::withCount('rencanaPanen')->get();

        $surplusTerbaru = RencanaPanen::with(['komoditas', 'petani', 'analisisSurplus'])
            ->where('status', 'aktif')
            ->where('potensi_surplus_kg', '>', 0)
            ->orderBy('tanggal_perkiraan_panen', 'asc')
            ->limit(6)
            ->get();

        $literatur = [
            [
                'judul' => 'Understanding Food Waste-Reducing Platforms: A Mini-Review',
                'penulis' => 'Fabio de Almeida Oroski & Jessica Monteiro da Silva (2023)',
                'jurnal' => 'Waste Management & Research',
                'fokus' => 'Platform digital penghubung pihak penyedia surplus pangan.',
                'kontribusi' => 'PANENPULIH memperluas fokus ke identifikasi potensi surplus sebelum masa panen (pre-harvest).',
                'doi' => 'https://doi.org/10.1177/0734242X221135248',
            ],
            [
                'judul' => 'Smart Food-Sharing Platforms for Social Sustainability: A Heuristic Algorithm Approach',
                'penulis' => 'Behzad Maleki Vishkaei & Pietro De Giovanni (2024)',
                'jurnal' => 'International Transactions in Operational Research',
                'fokus' => 'Algoritma pencocokan multi-kriteria: harga, masa simpan, transportasi, dan penawaran-permintaan.',
                'kontribusi' => 'Landasan bobot formula pencocokan pembeli M = 0.30C + 0.20Q + 0.20L + 0.20T + 0.10J.',
                'doi' => 'https://doi.org/10.1111/itor.13543',
            ],
            [
                'judul' => 'What Factors Impact the Adoption of Postharvest Loss-Reduction Technologies in Mangosteen Supply Chain?',
                'penulis' => 'Diyah Ratna Fauziana, Marimin, Heny Kuswanti Suwarsinah, Eko Agus Prasetio (2023)',
                'jurnal' => 'Journal of Open Innovation: Technology, Market, and Complexity',
                'fokus' => 'Adopsi teknologi pengurangan kehilangan hasil panen pada komoditas buah di Jawa Barat.',
                'kontribusi' => 'Mempertimbangkan fasilitas penyimpanan dan kemudahan penanganan petani lokal.',
                'doi' => 'https://doi.org/10.1016/j.joitmc.2023.100102',
            ],
            [
                'judul' => 'Circular Supply Chain Governance for Sustainable Fresh Agricultural Products',
                'penulis' => 'Tomy Perdana, Kusnandar, Hanifa Hasna Perdana, Fernianda Rahayu Hermiatin (2023)',
                'jurnal' => 'Sustainable Production and Consumption',
                'fokus' => 'Tata kelola rantai pasok sirkular untuk mengurangi food loss dan menyerap produk imperfect.',
                'kontribusi' => 'Menghubungkan petani langsung dengan pedagang pasar dan UMKM pengolah makanan lokal.',
                'doi' => 'https://doi.org/10.1016/j.spc.2023.09.001',
            ],
            [
                'judul' => 'Tinjauan Strategi Pengemasan Buah dan Sayur dalam Memerangi Food Loss Pascapanen',
                'penulis' => 'Taufiq Ihsan & Vioni Derosya (2024)',
                'jurnal' => 'Jurnal Ilmu Lingkungan',
                'fokus' => 'Karakteristik umur simpan dan penanganan komoditas hortikultura mudah rusak di Indonesia.',
                'kontribusi' => 'Indikator kerentanan komoditas (U) dan penyesuaian waktu distribusi pra-panen.',
                'doi' => 'https://doi.org/10.14710/jil.22.4.1078-1087',
            ],
        ];

        return view('landing', compact('metrik', 'komoditasList', 'surplusTerbaru', 'literatur'));
    }

    /**
     * Endpoint API simulasi live kalkulator untuk publik / landing page
     */
    public function hitungSimulasi(Request $request, SurplusIntelligenceService $service): JsonResponse
    {
        $h = (float) $request->input('estimasi_hasil_kg', 1000);
        $p = (float) $request->input('jumlah_terpesan_kg', 600);
        $k = (float) $request->input('jumlah_alokasi_lain_kg', 50);
        $hariPanen = (int) $request->input('hari_menuju_panen', 5);
        $skorU = (float) $request->input('skor_kerentanan', 85);
        $fasilitas = (string) $request->input('fasilitas_penyimpanan', 'shaded_shed');
        $permintaanPasarKg = (float) $request->input('permintaan_pasar_kg', 400);

        // S = H - P - K
        $s = max(0.0, $h - $p - $k);
        $persentaseSurplus = $h > 0 ? ($s / $h) * 100.0 : 0.0;
        $skorSp = min(100.0, $persentaseSurplus);

        // Skor W
        $skorW = match (true) {
            $hariPanen <= 0 => 100.0,
            $hariPanen <= 3 => 95.0,
            $hariPanen <= 7 => 80.0,
            $hariPanen <= 14 => 60.0,
            $hariPanen <= 21 => 40.0,
            $hariPanen <= 30 => 25.0,
            default => 10.0,
        };

        // Skor G
        $skorG = match ($fasilitas) {
            'none', 'open_air' => 95.0,
            'shaded_shed' => 65.0,
            'ventilated_warehouse' => 35.0,
            'cold_storage' => 10.0,
            default => 70.0,
        };

        // Skor D
        $rasioD = $s > 0 ? ($permintaanPasarKg / $s) : 2.0;
        $skorD = match (true) {
            $rasioD >= 2.0 => 15.0,
            $rasioD >= 1.0 => 35.0,
            $rasioD >= 0.5 => 65.0,
            default => 85.0,
        };

        // R = 0.25*Sp + 0.20*W + 0.25*U + 0.15*G + 0.15*D
        $skorRisiko = (0.25 * $skorSp) + (0.20 * $skorW) + (0.25 * $skorU) + (0.15 * $skorG) + (0.15 * $skorD);
        $skorRisiko = round(min(100.0, max(0.0, $skorRisiko)), 1);

        $kategori = $service->tentukanKategoriRisiko($skorRisiko);

        return response()->json([
            'potensi_surplus_kg' => round($s, 2),
            'persentase_surplus' => round($persentaseSurplus, 1),
            'skor_sp' => round($skorSp, 1),
            'skor_w' => round($skorW, 1),
            'skor_u' => round($skorU, 1),
            'skor_g' => round($skorG, 1),
            'skor_d' => round($skorD, 1),
            'skor_risiko_total' => $skorRisiko,
            'kategori_risiko' => $kategori,
        ]);
    }
}
