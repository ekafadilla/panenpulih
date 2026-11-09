<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use App\Models\RencanaPanen;
use App\Models\RiwayatStatus;
use App\Services\BuyerMatchingService;
use App\Services\SurplusIntelligenceService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RencanaPanenController extends Controller
{
    public function index(Request $request): View
    {
        $petaniId = Auth::id();

        $query = RencanaPanen::with(['komoditas', 'analisisSurplus'])
            ->where('petani_id', $petaniId);

        if ($request->filled('komoditas_id')) {
            $query->where('komoditas_id', $request->komoditas_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rencanaList = $query->orderBy('tanggal_perkiraan_panen', 'asc')->paginate(10);
        $komoditasList = Komoditas::all();

        return view('petani.rencana_panen.index', compact('rencanaList', 'komoditasList'));
    }

    public function create(): View
    {
        $komoditasList = Komoditas::all();
        $user = Auth::user();
        $petaniProfile = $user->petaniProfile;

        return view('petani.rencana_panen.create', compact('komoditasList', 'user', 'petaniProfile'));
    }

    public function store(
        Request $request,
        SurplusIntelligenceService $intelligenceService,
        BuyerMatchingService $matchingService
    ): RedirectResponse {
        $validated = $request->validate([
            'komoditas_id' => ['required', 'exists:komoditas,id'],
            'luas_lahan_m2' => ['required', 'numeric', 'min:10'],
            'estimasi_hasil_kg' => ['required', 'numeric', 'min:1'],
            'jumlah_terpesan_kg' => ['required', 'numeric', 'min:0'],
            'jumlah_alokasi_lain_kg' => ['required', 'numeric', 'min:0'],
            'tanggal_tanam' => ['nullable', 'date'],
            'tanggal_perkiraan_panen' => ['required', 'date'],
            'lokasi' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'kondisi_tanaman' => ['required', 'in:sangat_baik,baik,sedang,terdampak_cuaca'],
            'grade' => ['required', 'in:grade_a,grade_b,campur,imperfect'],
            'fasilitas_penyimpanan' => ['required', 'in:open_air,shaded_shed,ventilated_warehouse,cold_storage'],
            'ekspektasi_harga_per_kg' => ['nullable', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string'],
        ]);

        $petaniId = Auth::id();
        $kode = 'RPN-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5));

        // Hitung Surplus S = H - P - K
        $h = (float) $validated['estimasi_hasil_kg'];
        $p = (float) $validated['jumlah_terpesan_kg'];
        $k = (float) $validated['jumlah_alokasi_lain_kg'];
        $surplusKg = max(0.0, $h - $p - $k);

        $rencana = RencanaPanen::create([
            'kode_rencana' => $kode,
            'petani_id' => $petaniId,
            'komoditas_id' => $validated['komoditas_id'],
            'luas_lahan_m2' => $validated['luas_lahan_m2'],
            'estimasi_hasil_kg' => $h,
            'jumlah_terpesan_kg' => $p,
            'jumlah_alokasi_lain_kg' => $k,
            'potensi_surplus_kg' => $surplusKg,
            'tanggal_tanam' => $validated['tanggal_tanam'] ?? null,
            'tanggal_perkiraan_panen' => $validated['tanggal_perkiraan_panen'],
            'lokasi' => $validated['lokasi'],
            'latitude' => $validated['latitude'] ?? Auth::user()->latitude,
            'longitude' => $validated['longitude'] ?? Auth::user()->longitude,
            'kondisi_tanaman' => $validated['kondisi_tanaman'],
            'grade' => $validated['grade'],
            'fasilitas_penyimpanan' => $validated['fasilitas_penyimpanan'],
            'ekspektasi_harga_per_kg' => $validated['ekspektasi_harga_per_kg'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
            'status' => 'aktif',
        ]);

        // Jalankan Surplus Intelligence & Matching Algorithm
        $intelligenceService->analisa($rencana);
        $matchingService->cocokanUntukRencanaPanen($rencana);

        RiwayatStatus::catat('rencana_panen', $rencana->id, null, 'aktif', $petaniId, 'Rencana panen baru berhasil dibuat & dianalisis.');

        return redirect()->route('petani.rencana-panen.show', $rencana->id)
            ->with('success', 'Rencana panen berhasil didaftarkan! Potensi surplus dan rekomendasi pembeli telah dihitung otomatis.');
    }

    public function show(int $id, BuyerMatchingService $matchingService): View
    {
        $rencana = RencanaPanen::with([
            'komoditas',
            'analisisSurplus',
            'penawaran.pembeli',
            'penawaran.penyaluran',
        ])
            ->where('petani_id', Auth::id())
            ->findOrFail($id);

        // Ambil rekomendasi calon pembeli berurut berdasarkan skor M tertinggi
        $rekomendasiList = $rencana->rekomendasiPembeli()
            ->with(['kebutuhanPembeli.pembeli.pembeliProfile', 'kebutuhanPembeli.komoditas'])
            ->orderBy('skor_total', 'desc')
            ->get();

        return view('petani.rencana_panen.show', compact('rencana', 'rekomendasiList'));
    }

    public function edit(int $id): View
    {
        $rencana = RencanaPanen::where('petani_id', Auth::id())->findOrFail($id);
        $komoditasList = Komoditas::all();

        return view('petani.rencana_panen.edit', compact('rencana', 'komoditasList'));
    }

    public function update(
        Request $request,
        int $id,
        SurplusIntelligenceService $intelligenceService,
        BuyerMatchingService $matchingService
    ): RedirectResponse {
        $rencana = RencanaPanen::where('petani_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'komoditas_id' => ['required', 'exists:komoditas,id'],
            'luas_lahan_m2' => ['required', 'numeric', 'min:10'],
            'estimasi_hasil_kg' => ['required', 'numeric', 'min:1'],
            'jumlah_terpesan_kg' => ['required', 'numeric', 'min:0'],
            'jumlah_alokasi_lain_kg' => ['required', 'numeric', 'min:0'],
            'tanggal_tanam' => ['nullable', 'date'],
            'tanggal_perkiraan_panen' => ['required', 'date'],
            'lokasi' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'kondisi_tanaman' => ['required', 'in:sangat_baik,baik,sedang,terdampak_cuaca'],
            'grade' => ['required', 'in:grade_a,grade_b,campur,imperfect'],
            'fasilitas_penyimpanan' => ['required', 'in:open_air,shaded_shed,ventilated_warehouse,cold_storage'],
            'ekspektasi_harga_per_kg' => ['nullable', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string'],
            'status' => ['required', 'in:aktif,panen,selesai,dibatalkan'],
        ]);

        $h = (float) $validated['estimasi_hasil_kg'];
        $p = (float) $validated['jumlah_terpesan_kg'];
        $k = (float) $validated['jumlah_alokasi_lain_kg'];
        $surplusKg = max(0.0, $h - $p - $k);

        $statusLama = $rencana->status;

        $rencana->update([
            'komoditas_id' => $validated['komoditas_id'],
            'luas_lahan_m2' => $validated['luas_lahan_m2'],
            'estimasi_hasil_kg' => $h,
            'jumlah_terpesan_kg' => $p,
            'jumlah_alokasi_lain_kg' => $k,
            'potensi_surplus_kg' => $surplusKg,
            'tanggal_tanam' => $validated['tanggal_tanam'] ?? null,
            'tanggal_perkiraan_panen' => $validated['tanggal_perkiraan_panen'],
            'lokasi' => $validated['lokasi'],
            'latitude' => $validated['latitude'] ?? $rencana->latitude,
            'longitude' => $validated['longitude'] ?? $rencana->longitude,
            'kondisi_tanaman' => $validated['kondisi_tanaman'],
            'grade' => $validated['grade'],
            'fasilitas_penyimpanan' => $validated['fasilitas_penyimpanan'],
            'ekspektasi_harga_per_kg' => $validated['ekspektasi_harga_per_kg'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
            'status' => $validated['status'],
        ]);

        if ($statusLama !== $rencana->status) {
            RiwayatStatus::catat('rencana_panen', $rencana->id, $statusLama, $rencana->status, Auth::id(), 'Pembaruan status rencana panen.');
        }

        // Hitung ulang intelligence & matching
        $intelligenceService->analisa($rencana);
        $matchingService->cocokanUntukRencanaPanen($rencana);

        return redirect()->route('petani.rencana-panen.show', $rencana->id)
            ->with('success', 'Rencana panen berhasil diperbarui!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $rencana = RencanaPanen::where('petani_id', Auth::id())->findOrFail($id);
        $rencana->update(['status' => 'dibatalkan']);

        RiwayatStatus::catat('rencana_panen', $rencana->id, 'aktif', 'dibatalkan', Auth::id(), 'Petani membatalkan rencana panen.');

        return redirect()->route('petani.rencana-panen.index')->with('info', 'Rencana panen telah dibatalkan.');
    }
}
