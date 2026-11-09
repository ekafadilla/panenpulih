<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\KebutuhanPembeli;
use App\Models\Komoditas;
use App\Models\RencanaPanen;
use App\Models\RiwayatStatus;
use App\Services\BuyerMatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KebutuhanController extends Controller
{
    public function index(): View
    {
        $kebutuhanList = KebutuhanPembeli::with(['komoditas'])
            ->where('pembeli_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('pembeli.kebutuhan.index', compact('kebutuhanList'));
    }

    public function create(): View
    {
        $komoditasList = Komoditas::all();
        $user = Auth::user();
        $profile = $user->pembeliProfile;

        return view('pembeli.kebutuhan.create', compact('komoditasList', 'user', 'profile'));
    }

    public function store(Request $request, BuyerMatchingService $matchingService): RedirectResponse
    {
        $validated = $request->validate([
            'komoditas_id' => ['required', 'exists:komoditas,id'],
            'jumlah_kg' => ['required', 'numeric', 'min:1'],
            'tanggal_mulai_butuh' => ['required', 'date'],
            'batas_tanggal_butuh' => ['required', 'date', 'after_or_equal:tanggal_mulai_butuh'],
            'grade_diterima' => ['required', 'in:semua,grade_a,grade_b,imperfect'],
            'budget_per_kg' => ['nullable', 'numeric', 'min:0'],
            'lokasi' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'tujuan_penggunaan' => ['required', 'in:pasar_tradisional,supermarket,olahan_makanan,restoran,food_bank'],
            'spesifikasi' => ['nullable', 'string'],
        ]);

        $kode = 'KBT-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5));

        $kebutuhan = KebutuhanPembeli::create([
            'kode_kebutuhan' => $kode,
            'pembeli_id' => Auth::id(),
            'komoditas_id' => $validated['komoditas_id'],
            'jumlah_kg' => $validated['jumlah_kg'],
            'jumlah_terpenuhi_kg' => 0,
            'tanggal_mulai_butuh' => $validated['tanggal_mulai_butuh'],
            'batas_tanggal_butuh' => $validated['batas_tanggal_butuh'],
            'grade_diterima' => $validated['grade_diterima'],
            'budget_per_kg' => $validated['budget_per_kg'] ?? null,
            'lokasi' => $validated['lokasi'],
            'latitude' => $validated['latitude'] ?? Auth::user()->latitude,
            'longitude' => $validated['longitude'] ?? Auth::user()->longitude,
            'tujuan_penggunaan' => $validated['tujuan_penggunaan'],
            'spesifikasi' => $validated['spesifikasi'] ?? null,
            'status' => 'aktif',
        ]);

        // Picu pencocokan ulang terhadap rencana panen aktif untuk komoditas ini
        $rencanaList = RencanaPanen::where('komoditas_id', $kebutuhan->komoditas_id)
            ->where('status', 'aktif')
            ->get();

        foreach ($rencanaList as $rencana) {
            $matchingService->cocokanUntukRencanaPanen($rencana);
        }

        RiwayatStatus::catat('kebutuhan', $kebutuhan->id, null, 'aktif', Auth::id(), 'Kebutuhan baru didaftarkan pembeli.');

        return redirect()->route('pembeli.jelajah-surplus', ['komoditas_id' => $kebutuhan->komoditas_id])
            ->with('success', "Kebutuhan {$kode} berhasil didaftarkan! Berikut rekomendasi surplus petani yang siap dipanen.");
    }

    public function edit(int $id): View
    {
        $kebutuhan = KebutuhanPembeli::where('pembeli_id', Auth::id())->findOrFail($id);
        $komoditasList = Komoditas::all();

        return view('pembeli.kebutuhan.edit', compact('kebutuhan', 'komoditasList'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $kebutuhan = KebutuhanPembeli::where('pembeli_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'komoditas_id' => ['required', 'exists:komoditas,id'],
            'jumlah_kg' => ['required', 'numeric', 'min:1'],
            'tanggal_mulai_butuh' => ['required', 'date'],
            'batas_tanggal_butuh' => ['required', 'date', 'after_or_equal:tanggal_mulai_butuh'],
            'grade_diterima' => ['required', 'in:semua,grade_a,grade_b,imperfect'],
            'budget_per_kg' => ['nullable', 'numeric', 'min:0'],
            'lokasi' => ['required', 'string', 'max:255'],
            'tujuan_penggunaan' => ['required', 'in:pasar_tradisional,supermarket,olahan_makanan,restoran,food_bank'],
            'spesifikasi' => ['nullable', 'string'],
            'status' => ['required', 'in:aktif,terpenuhi,dibatalkan'],
        ]);

        $kebutuhan->update($validated);

        return redirect()->route('pembeli.kebutuhan.index')->with('success', 'Kebutuhan berhasil diperbarui!');
    }
}
