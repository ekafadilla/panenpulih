<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalisisSurplus;
use App\Models\Komoditas;
use App\Models\Penawaran;
use App\Models\Penyaluran;
use App\Models\RencanaPanen;
use App\Models\User;
use App\Services\SurplusImpactService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(SurplusImpactService $impactService): View
    {
        $metrik = $impactService->getMetrikPlatform();

        $totalPetani = User::where('role', 'petani')->count();
        $totalPedagang = User::where('role', 'pedagang')->count();
        $totalUmkm = User::where('role', 'umkm')->count();
        $totalRencanaPanen = RencanaPanen::count();
        $totalPenawaran = Penawaran::count();

        // Data komoditas dengan surplus tertinggi
        $komoditasSurplus = Komoditas::withSum(['rencanaPanen as total_surplus' => function ($q) {
            $q->where('status', 'aktif');
        }], 'potensi_surplus_kg')
            ->orderByDesc('total_surplus')
            ->get();

        // Riwayat Penyaluran Terbaru
        $penyaluranTerbaru = Penyaluran::with([
            'penawaran.rencanaPanen.komoditas',
            'penawaran.petani',
            'penawaran.pembeli',
        ])->latest()->limit(5)->get();

        // Data Koordinat Peta Interaktif (Petani, Pembeli, Hotspot Surplus)
        $petaPins = [];

        $semuaRencana = RencanaPanen::with(['komoditas', 'petani', 'analisisSurplus'])
            ->where('status', 'aktif')
            ->get();

        foreach ($semuaRencana as $rp) {
            $petaPins[] = [
                'tipe' => 'petani',
                'judul' => $rp->komoditas->nama . ' (' . $rp->petani->name . ')',
                'lokasi' => $rp->lokasi,
                'lat' => (float) ($rp->latitude ?? -6.3264),
                'lng' => (float) ($rp->longitude ?? 108.3200),
                'surplus_kg' => (float) $rp->potensi_surplus_kg,
                'risiko' => $rp->analisisSurplus?->kategori_risiko ?? 'Sedang',
                'tgl_panen' => Carbon::parse($rp->tanggal_perkiraan_panen)->isoFormat('D MMM Y'),
            ];
        }

        $semuaPembeli = User::with('pembeliProfile')
            ->whereIn('role', ['pedagang', 'umkm'])
            ->get();

        foreach ($semuaPembeli as $pmb) {
            $petaPins[] = [
                'tipe' => $pmb->role,
                'judul' => ($pmb->institution ?? $pmb->name) . ' (' . strtoupper($pmb->role) . ')',
                'lokasi' => $pmb->address,
                'lat' => (float) ($pmb->latitude ?? -6.7120),
                'lng' => (float) ($pmb->longitude ?? 108.5123),
                'surplus_kg' => 0,
                'risiko' => 'Mitra',
                'tgl_panen' => '-',
            ];
        }

        return view('admin.dashboard', compact(
            'metrik',
            'totalPetani',
            'totalPedagang',
            'totalUmkm',
            'totalRencanaPanen',
            'totalPenawaran',
            'komoditasSurplus',
            'penyaluranTerbaru',
            'petaPins'
        ));
    }
}
