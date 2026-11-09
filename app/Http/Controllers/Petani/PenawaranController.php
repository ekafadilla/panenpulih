<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\KebutuhanPembeli;
use App\Models\Penawaran;
use App\Models\RencanaPanen;
use App\Models\RiwayatStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PenawaranController extends Controller
{
    public function index(Request $request): View
    {
        $petaniId = Auth::id();

        $query = Penawaran::with([
            'rencanaPanen.komoditas',
            'pembeli.pembeliProfile',
            'penyaluran',
        ])->where('petani_id', $petaniId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $penawaranList = $query->latest()->paginate(10);

        return view('petani.penawaran.index', compact('penawaranList'));
    }

    public function ajukan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rencana_panen_id' => ['required', 'exists:rencana_panen,id'],
            'kebutuhan_pembeli_id' => ['required', 'exists:kebutuhan_pembeli,id'],
            'jumlah_kg' => ['required', 'numeric', 'min:1'],
            'harga_per_kg' => ['required', 'numeric', 'min:100'],
            'tanggal_pengambilan' => ['required', 'date'],
            'metode_pengiriman' => ['required', 'in:diambil_pembeli,dikirim_petani,jasa_angkut'],
            'catatan' => ['nullable', 'string'],
        ]);

        $petaniId = Auth::id();
        $rencana = RencanaPanen::where('petani_id', $petaniId)->findOrFail($validated['rencana_panen_id']);
        $kebutuhan = KebutuhanPembeli::findOrFail($validated['kebutuhan_pembeli_id']);

        $kode = 'PNW-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5));
        $totalHarga = (float) $validated['jumlah_kg'] * (float) $validated['harga_per_kg'];

        $penawaran = Penawaran::create([
            'kode_penawaran' => $kode,
            'rencana_panen_id' => $rencana->id,
            'kebutuhan_pembeli_id' => $kebutuhan->id,
            'petani_id' => $petaniId,
            'pembeli_id' => $kebutuhan->pembeli_id,
            'inisiator_role' => 'petani',
            'jumlah_kg' => $validated['jumlah_kg'],
            'harga_per_kg' => $validated['harga_per_kg'],
            'total_harga' => $totalHarga,
            'tanggal_pengambilan' => $validated['tanggal_pengambilan'],
            'metode_pengiriman' => $validated['metode_pengiriman'],
            'catatan' => $validated['catatan'] ?? null,
            'status' => 'menunggu_konfirmasi',
        ]);

        RiwayatStatus::catat('penawaran', $penawaran->id, null, 'menunggu_konfirmasi', $petaniId, 'Petani mengajukan penawaran surplus ke pembeli.');

        return back()->with('success', "Penawaran {$kode} sebesar {$validated['jumlah_kg']} kg berhasil diajukan ke pembeli!");
    }

    public function terima(int $id): RedirectResponse
    {
        $penawaran = Penawaran::where('petani_id', Auth::id())->findOrFail($id);
        $statusLama = $penawaran->status;

        $penawaran->update(['status' => 'disetujui']);

        RiwayatStatus::catat('penawaran', $penawaran->id, $statusLama, 'disetujui', Auth::id(), 'Petani menyetujui pengajuan pembelian.');

        return back()->with('success', "Penawaran {$penawaran->kode_penawaran} berhasil disetujui! Silakan siapkan penyaluran saat panen.");
    }

    public function tolak(Request $request, int $id): RedirectResponse
    {
        $penawaran = Penawaran::where('petani_id', Auth::id())->findOrFail($id);
        $statusLama = $penawaran->status;

        $alasan = $request->input('alasan', 'Petani tidak dapat memenuhi kuantitas/harga yang diajukan saat ini.');

        $penawaran->update(['status' => 'ditolak']);

        RiwayatStatus::catat('penawaran', $penawaran->id, $statusLama, 'ditolak', Auth::id(), "Penawaran ditolak: {$alasan}");

        return back()->with('info', "Penawaran {$penawaran->kode_penawaran} telah ditolak.");
    }
}
