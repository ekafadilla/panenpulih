@extends('layouts.app')

@section('title', 'Edit Kebutuhan - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="mb-4 pb-3 border-bottom">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-1">
                <li class="breadcrumb-item"><a href="{{ route('pembeli.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pembeli.kebutuhan.index') }}">Kebutuhan</a></li>
                <li class="breadcrumb-item active">{{ $kebutuhan->kode_kebutuhan }}</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-0">Perbarui Kebutuhan: {{ $kebutuhan->kode_kebutuhan }}</h2>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom p-4 bg-white">
                <form action="{{ route('pembeli.kebutuhan.update', $kebutuhan->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Komoditas <span class="text-danger">*</span></label>
                            <select name="komoditas_id" class="form-select" required>
                                @foreach($komoditasList as $k)
                                    <option value="{{ $k->id }}" {{ $kebutuhan->komoditas_id == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jumlah Kebutuhan (kg) <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_kg" class="form-control" value="{{ old('jumlah_kg', $kebutuhan->jumlah_kg) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Mulai Butuh <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai_butuh" class="form-control" value="{{ old('tanggal_mulai_butuh', $kebutuhan->tanggal_mulai_butuh->toDateString()) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Batas Akhir Waktu <span class="text-danger">*</span></label>
                            <input type="date" name="batas_tanggal_butuh" class="form-control" value="{{ old('batas_tanggal_butuh', $kebutuhan->batas_tanggal_butuh->toDateString()) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Grade Diterima</label>
                            <select name="grade_diterima" class="form-select">
                                <option value="semua" {{ $kebutuhan->grade_diterima === 'semua' ? 'selected' : '' }}>Semua Grade</option>
                                <option value="grade_a" {{ $kebutuhan->grade_diterima === 'grade_a' ? 'selected' : '' }}>Grade A</option>
                                <option value="grade_b" {{ $kebutuhan->grade_diterima === 'grade_b' ? 'selected' : '' }}>Grade B</option>
                                <option value="imperfect" {{ $kebutuhan->grade_diterima === 'imperfect' ? 'selected' : '' }}>Imperfect</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Peruntukan</label>
                            <select name="tujuan_penggunaan" class="form-select">
                                <option value="olahan_makanan" {{ $kebutuhan->tujuan_penggunaan === 'olahan_makanan' ? 'selected' : '' }}>Olahan Pangan</option>
                                <option value="pasar_tradisional" {{ $kebutuhan->tujuan_penggunaan === 'pasar_tradisional' ? 'selected' : '' }}>Pasar Tradisional</option>
                                <option value="supermarket" {{ $kebutuhan->tujuan_penggunaan === 'supermarket' ? 'selected' : '' }}>Supermarket</option>
                                <option value="restoran" {{ $kebutuhan->tujuan_penggunaan === 'restoran' ? 'selected' : '' }}>Restoran</option>
                                <option value="food_bank" {{ $kebutuhan->tujuan_penggunaan === 'food_bank' ? 'selected' : '' }}>Food Bank</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Budget (Rp/kg)</label>
                            <input type="number" name="budget_per_kg" class="form-control" value="{{ old('budget_per_kg', $kebutuhan->budget_per_kg) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Lokasi Gudang</label>
                            <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi', $kebutuhan->lokasi) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Status</label>
                            <select name="status" class="form-select">
                                <option value="aktif" {{ $kebutuhan->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="terpenuhi" {{ $kebutuhan->status === 'terpenuhi' ? 'selected' : '' }}>Terpenuhi</option>
                                <option value="dibatalkan" {{ $kebutuhan->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Spesifikasi</label>
                            <textarea name="spesifikasi" class="form-control" rows="2">{{ old('spesifikasi', $kebutuhan->spesifikasi) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <button type="submit" class="btn btn-pp-primary px-4 fw-bold">Simpan Pembaruan</button>
                        <a href="{{ route('pembeli.kebutuhan.index') }}" class="btn btn-light border">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
