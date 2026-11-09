<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\KebutuhanPembeli;
use App\Models\Penawaran;
use App\Models\Penyaluran;
use App\Models\RencanaPanen;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $pembeliId = Auth::id();
        $user = Auth::user();

        $kebutuhanAktif = KebutuhanPembeli::with('komoditas')
            ->where('pembeli_id', $pembeliId)
            ->where('status', 'aktif')
            ->get();

        $totalKgDibutuhkan = (float) $kebutuhanAktif->sum('jumlah_kg');
        $komoditasIds = $kebutuhanAktif->pluck('komoditas_id')->toArray();

        // Cari surplus yang cocok dengan komoditas yang dibutuhkan
        $surplusCocok = RencanaPanen::with(['komoditas', 'petani', 'analisisSurplus'])
            ->where('status', 'aktif')
            ->where('potensi_surplus_kg', '>', 0)
            ->when(! empty($komoditasIds), function ($q) use ($komoditasIds) {
                $q->whereIn('komoditas_id', $komoditasIds);
            })
            ->orderBy('tanggal_perkiraan_panen', 'asc')
            ->limit(6)
            ->get();

        // Penawaran aktif
        $penawaranBerjalan = Penawaran::with(['rencanaPanen.komoditas', 'petani'])
            ->where('pembeli_id', $pembeliId)
            ->latest()
            ->limit(5)
            ->get();

        // Penyaluran diterima
        $totalKgDiterima = (float) Penyaluran::whereHas('penawaran', function ($q) use ($pembeliId) {
            $q->where('pembeli_id', $pembeliId);
        })->whereIn('status_verifikasi', ['terverifikasi', 'selesai'])->sum('jumlah_realisasi_kg');

        return view('pembeli.dashboard', compact(
            'user',
            'kebutuhanAktif',
            'totalKgDibutuhkan',
            'surplusCocok',
            'penawaranBerjalan',
            'totalKgDiterima'
        ));
    }
}
