<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Penawaran;
use App\Models\RencanaPanen;
use App\Models\RiwayatStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PenawaranController extends Controller
{
    public function index(): View
    {
        $penawaranList = Penawaran::with([
            'rencanaPanen.komoditas',
            'petani',
            'penyaluran',
        ])
            ->where('pembeli_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('pembeli.penawaran.index', compact('penawaranList'));
    }

    public function buat(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rencana_panen_id' => ['required', 'exists:rencana_panen,id'],
            'kebutuhan_pembeli_id' => ['nullable', 'exists:kebutuhan_pembeli,id'],
            'jumlah_kg' => ['required', 'numeric', 'min:1'],
            'harga_per_kg' => ['required', 'numeric', 'min:100'],
            'tanggal_pengambilan' => ['required', 'date'],
            'metode_pengiriman' => ['required', 'in:diambil_pembeli,dikirim_petani,jasa_angkut'],
            'catatan' => ['nullable', 'string'],
        ]);

        $pembeli = Auth::user();
        $rencana = RencanaPanen::findOrFail($validated['rencana_panen_id']);

        $kode = 'PNW-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5));
        $totalHarga = (float) $validated['jumlah_kg'] * (float) $validated['harga_per_kg'];

        $penawaran = Penawaran::create([
            'kode_penawaran' => $kode,
            'rencana_panen_id' => $rencana->id,
            'kebutuhan_pembeli_id' => $validated['kebutuhan_pembeli_id'] ?? null,
            'petani_id' => $rencana->petani_id,
            'pembeli_id' => $pembeli->id,
            'inisiator_role' => $pembeli->role,
            'jumlah_kg' => $validated['jumlah_kg'],
            'harga_per_kg' => $validated['harga_per_kg'],
            'total_harga' => $totalHarga,
            'tanggal_pengambilan' => $validated['tanggal_pengambilan'],
            'metode_pengiriman' => $validated['metode_pengiriman'],
            'catatan' => $validated['catatan'] ?? null,
            'status' => 'menunggu_konfirmasi',
        ]);

        RiwayatStatus::catat('penawaran', $penawaran->id, null, 'menunggu_konfirmasi', $pembeli->id, "Pembeli mengajukan penawaran {$validated['jumlah_kg']} kg untuk rencana panen {$rencana->kode_rencana}.");

        return redirect()->route('pembeli.penawaran.index')
            ->with('success', "Pengajuan pembelian {$kode} sebesar {$validated['jumlah_kg']} kg berhasil dikirim ke Petani ({$rencana->petani->name}). Menunggu konfirmasi petani.");
    }

    public function batalkan(int $id): RedirectResponse
    {
        $penawaran = Penawaran::where('pembeli_id', Auth::id())->findOrFail($id);

        if ($penawaran->status === 'menunggu_konfirmasi') {
            $penawaran->update(['status' => 'dibatalkan']);
            RiwayatStatus::catat('penawaran', $penawaran->id, 'menunggu_konfirmasi', 'dibatalkan', Auth::id(), 'Pembeli membatalkan penawaran.');

            return back()->with('info', "Penawaran {$penawaran->kode_penawaran} telah dibatalkan.");
        }

        return back()->with('error', 'Penawaran ini tidak dapat dibatalkan.');
    }
}
