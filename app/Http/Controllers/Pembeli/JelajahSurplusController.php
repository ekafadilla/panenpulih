<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\KebutuhanPembeli;
use App\Models\Komoditas;
use App\Models\RencanaPanen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class JelajahSurplusController extends Controller
{
    public function index(Request $request): View
    {
        $query = RencanaPanen::with(['komoditas', 'petani', 'analisisSurplus'])
            ->where('status', 'aktif')
            ->where('potensi_surplus_kg', '>', 0);

        if ($request->filled('komoditas_id')) {
            $query->where('komoditas_id', $request->komoditas_id);
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        if ($request->filled('kategori_risiko')) {
            $query->whereHas('analisisSurplus', function ($q) use ($request) {
                $q->where('kategori_risiko', $request->kategori_risiko);
            });
        }

        if ($request->filled('urgensi')) {
            $hari = (int) $request->urgensi;
            $maxDate = Carbon::now()->addDays($hari)->toDateString();
            $query->where('tanggal_perkiraan_panen', '<=', $maxDate);
        }

        $surplusList = $query->orderBy('tanggal_perkiraan_panen', 'asc')->paginate(9);
        $komoditasList = Komoditas::all();

        // Kebutuhan pembeli aktif untuk opsi dropdown "Gunakan Kebutuhan Saya"
        $kebutuhanSaya = KebutuhanPembeli::where('pembeli_id', Auth::id())
            ->where('status', 'aktif')
            ->get();

        // Data titik koordinat untuk Leaflet Map
        $mapMarkers = $surplusList->map(function ($item) {
            return [
                'id' => $item->id,
                'kode' => $item->kode_rencana,
                'komoditas' => $item->komoditas->nama,
                'petani' => $item->petani->name,
                'surplus_kg' => $item->potensi_surplus_kg,
                'tgl_panen' => Carbon::parse($item->tanggal_perkiraan_panen)->isoFormat('D MMM Y'),
                'hari_lagi' => max(0, (int) Carbon::now()->diffInDays(Carbon::parse($item->tanggal_perkiraan_panen), false)),
                'risiko' => $item->analisisSurplus?->kategori_risiko ?? 'Sedang',
                'lat' => (float) ($item->latitude ?? -6.2088),
                'lng' => (float) ($item->longitude ?? 106.8456),
                'harga' => $item->ekspektasi_harga_per_kg ? 'Rp ' . number_format($item->ekspektasi_harga_per_kg, 0, ',', '.') . '/kg' : 'Negosiasi',
            ];
        });

        return view('pembeli.jelajah.index', compact('surplusList', 'komoditasList', 'kebutuhanSaya', 'mapMarkers'));
    }

    public function detail(int $id): View
    {
        $rencana = RencanaPanen::with(['komoditas', 'petani', 'analisisSurplus'])
            ->where('status', 'aktif')
            ->findOrFail($id);

        $kebutuhanSaya = KebutuhanPembeli::where('pembeli_id', Auth::id())
            ->where('komoditas_id', $rencana->komoditas_id)
            ->where('status', 'aktif')
            ->get();

        return view('pembeli.jelajah.detail', compact('rencana', 'kebutuhanSaya'));
    }
}
