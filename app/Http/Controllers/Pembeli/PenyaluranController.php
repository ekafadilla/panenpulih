<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Penyaluran;
use App\Models\RiwayatStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PenyaluranController extends Controller
{
    public function index(): View
    {
        $pembeliId = Auth::id();

        $penyaluranList = Penyaluran::with([
            'penawaran.rencanaPanen.komoditas',
            'penawaran.petani',
        ])
            ->whereHas('penawaran', function ($q) use ($pembeliId) {
                $q->where('pembeli_id', $pembeliId);
            })
            ->latest()
            ->paginate(10);

        return view('pembeli.penyaluran.index', compact('penyaluranList'));
    }

    public function konfirmasi(Request $request, int $id): RedirectResponse
    {
        $pembeliId = Auth::id();

        $penyaluran = Penyaluran::whereHas('penawaran', function ($q) use ($pembeliId) {
            $q->where('pembeli_id', $pembeliId);
        })->findOrFail($id);

        $validated = $request->validate([
            'rating_pembeli' => ['required', 'integer', 'between:1,5'],
            'ulasan_pembeli' => ['nullable', 'string'],
        ]);

        $penyaluran->update([
            'status_verifikasi' => 'selesai',
            'rating_pembeli' => $validated['rating_pembeli'],
            'ulasan_pembeli' => $validated['ulasan_pembeli'] ?? null,
        ]);

        RiwayatStatus::catat('penyaluran', $penyaluran->id, 'terverifikasi', 'selesai', Auth::id(), 'Pembeli telah mengonfirmasi penerimaan dan memberikan ulasan bintang ' . $validated['rating_pembeli'] . '.');

        return back()->with('success', 'Terima kasih! Konfirmasi penerimaan dan penilaian berhasil disimpan.');
    }
}
