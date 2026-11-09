<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penyaluran;
use App\Models\RiwayatStatus;
use App\Services\SurplusImpactService;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function laporanSdg(SurplusImpactService $impactService): View
    {
        $metrik = $impactService->getMetrikPlatform();

        $semuaPenyaluran = Penyaluran::with([
            'penawaran.rencanaPanen.komoditas',
            'penawaran.petani',
            'penawaran.pembeli',
        ])
            ->whereIn('status_verifikasi', ['terverifikasi', 'selesai'])
            ->latest()
            ->get();

        return view('admin.laporan.sdg', compact('metrik', 'semuaPenyaluran'));
    }

    public function auditLog(): View
    {
        $logs = RiwayatStatus::with('user')->latest()->paginate(20);

        return view('admin.laporan.audit_log', compact('logs'));
    }
}
