<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\Penawaran;
use App\Models\Penyaluran;
use App\Models\RencanaPanen;
use App\Services\SurplusImpactService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(SurplusImpactService $impactService): View
    {
        $user = Auth::user();
        $petaniId = $user->id;

        // Metrik khusus petani
        $metrikPetani = $impactService->getMetrikPetani($petaniId);

        $totalRencana = RencanaPanen::where('petani_id', $petaniId)->count();
        $rencanaAktif = RencanaPanen::with(['komoditas', 'analisisSurplus', 'rekomendasiPembeli'])
            ->where('petani_id', $petaniId)
            ->where('status', 'aktif')
            ->orderBy('tanggal_perkiraan_panen', 'asc')
            ->get();

        // Rencana dengan risiko Tinggi / Sangat Tinggi
        $peringatanRisiko = $rencanaAktif->filter(function ($item) {
            return in_array($item->analisisSurplus?->kategori_risiko, ['Tinggi', 'Sangat Tinggi'], true);
        });

        // Penawaran yang menunggu konfirmasi petani
        $penawaranMasuk = Penawaran::with(['pembeli', 'rencanaPanen.komoditas', 'kebutuhanPembeli'])
            ->where('petani_id', $petaniId)
            ->where('status', 'menunggu_konfirmasi')
            ->where('inisiator_role', '!=', 'petani')
            ->latest()
            ->get();

        // Penyaluran terbaru
        $penyaluranTerbaru = Penyaluran::with(['penawaran.pembeli', 'penawaran.rencanaPanen.komoditas'])
            ->whereHas('penawaran', function ($q) use ($petaniId) {
                $q->where('petani_id', $petaniId);
            })
            ->latest()
            ->limit(5)
            ->get();

        return view('petani.dashboard', compact(
            'user',
            'metrikPetani',
            'totalRencana',
            'rencanaAktif',
            'peringatanRisiko',
            'penawaranMasuk',
            'penyaluranTerbaru'
        ));
    }
}
