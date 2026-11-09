<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KomoditasController extends Controller
{
    public function index(): View
    {
        $komoditasList = Komoditas::withCount('rencanaPanen')->paginate(10);

        return view('admin.komoditas.index', compact('komoditasList'));
    }

    public function create(): View
    {
        return view('admin.komoditas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nama_latin' => ['nullable', 'string', 'max:255'],
            'kategori' => ['required', 'in:sayuran,buah,palawija,rempah'],
            'umur_simpan_hari' => ['required', 'integer', 'min:1'],
            'skor_kerentanan' => ['required', 'integer', 'between:0,100'],
            'satuan' => ['required', 'string', 'max:10'],
            'harga_acuan_per_kg' => ['required', 'numeric', 'min:100'],
            'deskripsi' => ['nullable', 'string'],
            'pedoman_penyimpanan' => ['nullable', 'string'],
        ]);

        Komoditas::create($validated);

        return redirect()->route('admin.komoditas.index')->with('success', "Komoditas {$validated['nama']} berhasil ditambahkan!");
    }

    public function edit(int $id): View
    {
        $komoditas = Komoditas::findOrFail($id);

        return view('admin.komoditas.edit', compact('komoditas'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $komoditas = Komoditas::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nama_latin' => ['nullable', 'string', 'max:255'],
            'kategori' => ['required', 'in:sayuran,buah,palawija,rempah'],
            'umur_simpan_hari' => ['required', 'integer', 'min:1'],
            'skor_kerentanan' => ['required', 'integer', 'between:0,100'],
            'satuan' => ['required', 'string', 'max:10'],
            'harga_acuan_per_kg' => ['required', 'numeric', 'min:100'],
            'deskripsi' => ['nullable', 'string'],
            'pedoman_penyimpanan' => ['nullable', 'string'],
        ]);

        $komoditas->update($validated);

        return redirect()->route('admin.komoditas.index')->with('success', "Komoditas {$komoditas->nama} berhasil diperbarui!");
    }

    public function destroy(int $id): RedirectResponse
    {
        $komoditas = Komoditas::findOrFail($id);

        if ($komoditas->rencanaPanen()->count() > 0) {
            return back()->with('error', 'Komoditas tidak dapat dihapus karena telah digunakan dalam rencana panen.');
        }

        $komoditas->delete();

        return redirect()->route('admin.komoditas.index')->with('info', "Komoditas {$komoditas->nama} telah dihapus.");
    }
}
