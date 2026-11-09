<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\Penawaran;
use App\Models\Penyaluran;
use App\Models\RiwayatStatus;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PenyaluranController extends Controller
{
    public function index(): View
    {
        $petaniId = Auth::id();

        $penyaluranList = Penyaluran::with([
            'penawaran.rencanaPanen.komoditas',
            'penawaran.pembeli.pembeliProfile',
        ])
            ->whereHas('penawaran', function ($q) use ($petaniId) {
                $q->where('petani_id', $petaniId);
            })
            ->latest()
            ->paginate(10);

        return view('petani.penyaluran.index', compact('penyaluranList'));
    }

    public function formCatat(int $penawaranId): View
    {
        $penawaran = Penawaran::with(['rencanaPanen.komoditas', 'pembeli.pembeliProfile'])
            ->where('petani_id', Auth::id())
            ->whereIn('status', ['disetujui', 'selesai'])
            ->findOrFail($penawaranId);

        return view('petani.penyaluran.catat', compact('penawaran'));
    }

    public function simpanCatat(Request $request, int $penawaranId): RedirectResponse
    {
        $penawaran = Penawaran::with('rencanaPanen')->where('petani_id', Auth::id())->findOrFail($penawaranId);
        $rencana = $penawaran->rencanaPanen;

        $validated = $request->validate([
            'tanggal_penyaluran' => ['required', 'date'],
            'jumlah_realisasi_kg' => ['required', 'numeric', 'min:1'],
            'harga_total_realisasi' => ['required', 'numeric', 'min:1000'],
            'hasil_aktual_kg' => ['nullable', 'numeric', 'min:1'],
            'status_kualitas' => ['required', 'in:sesuai_ekspektasi,cacat_ringan,diolah_lanjut'],
            'catatan_penyaluran' => ['nullable', 'string'],
        ]);

        $realisasiKg = (float) $validated['jumlah_realisasi_kg'];
        $hargaTotal = (float) $validated['harga_total_realisasi'];

        // Hitung surplus aktual jika petani memperbarui hasil aktual
        $hasilAktual = $validated['hasil_aktual_kg'] ?? $rencana->estimasi_hasil_kg;
        $surplusAktual = max(1.0, (float) $hasilAktual - (float) $rencana->jumlah_terpesan_kg - (float) $rencana->jumlah_alokasi_lain_kg);

        // Sisa surplus belum terserap
        $sisaSurplus = max(0.0, $surplusAktual - $realisasiKg);

        // Rumus Tingkat Penyerapan (Section 11): (jumlah berhasil disalurkan / potensi surplus aktual) * 100%
        $tingkatPenyerapan = round(($realisasiKg / $surplusAktual) * 100.0, 2);
        $tingkatPenyerapan = min(100.0, $tingkatPenyerapan);

        $kodePenyaluran = 'DSTR-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5));

        $penyaluran = Penyaluran::create([
            'kode_penyaluran' => $kodePenyaluran,
            'penawaran_id' => $penawaran->id,
            'tanggal_penyaluran' => $validated['tanggal_penyaluran'],
            'jumlah_rencana_kg' => $penawaran->jumlah_kg,
            'jumlah_realisasi_kg' => $realisasiKg,
            'sisa_surplus_kg' => $sisaSurplus,
            'tingkat_penyerapan_persen' => $tingkatPenyerapan,
            'harga_total_realisasi' => $hargaTotal,
            'status_kualitas' => $validated['status_kualitas'],
            'catatan_penyaluran' => $validated['catatan_penyaluran'] ?? null,
            'status_verifikasi' => 'terverifikasi',
            'dampak_food_loss_terhindar_kg' => $realisasiKg,
            'dampak_nilai_ekonomi_rp' => $hargaTotal,
        ]);

        // Perbarui status penawaran & rencana panen
        $penawaran->update(['status' => 'selesai']);
        if ($validated['hasil_aktual_kg']) {
            $rencana->update([
                'hasil_aktual_kg' => $validated['hasil_aktual_kg'],
                'tanggal_panen_aktual' => $validated['tanggal_penyaluran'],
                'status' => 'selesai',
            ]);
        }

        RiwayatStatus::catat('penyaluran', $penyaluran->id, null, 'terverifikasi', Auth::id(), "Realisasi penyaluran {$realisasiKg} kg dicatat. Tingkat penyerapan: {$tingkatPenyerapan}%.");

        return redirect()->route('petani.penyaluran.index')
            ->with('success', "Penyaluran {$kodePenyaluran} berhasil dicatat! Tingkat penyerapan surplus: {$tingkatPenyerapan}%.");
    }
}
