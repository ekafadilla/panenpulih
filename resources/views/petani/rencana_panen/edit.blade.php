@extends('layouts.app')

@section('title', 'Edit Rencana Panen - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="mb-4 pb-3 border-bottom">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-1">
                <li class="breadcrumb-item"><a href="{{ route('petani.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('petani.rencana-panen.index') }}">Rencana Panen</a></li>
                <li class="breadcrumb-item"><a href="{{ route('petani.rencana-panen.show', $rencana->id) }}">{{ $rencana->kode_rencana }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-0">Perbarui Rencana Panen: {{ $rencana->kode_rencana }}</h2>
        <small class="text-muted">Perbarui estimasi hasil panen menjelang hari panen untuk menghitung ulang surplus & risiko</small>
    </div>

    <form action="{{ route('petani.rencana-panen.update', $rencana->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">
                <div class="card-custom p-4 bg-white mb-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-pencil-square text-pp-primary me-2"></i> Perbarui Data Panen</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Komoditas <span class="text-danger">*</span></label>
                            <select name="komoditas_id" class="form-select" required>
                                @foreach($komoditasList as $k)
                                    <option value="{{ $k->id }}" {{ $rencana->komoditas_id == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama }} (Daya simpan: ~{{ $k->umur_simpan_hari }} hari)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Luas Lahan Panen (m²) <span class="text-danger">*</span></label>
                            <input type="number" name="luas_lahan_m2" class="form-control" value="{{ old('luas_lahan_m2', $rencana->luas_lahan_m2) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Estimasi Hasil Panen (H) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="estimasi_hasil_kg" class="form-control" value="{{ old('estimasi_hasil_kg', $rencana->estimasi_hasil_kg) }}" required>
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jumlah Terpesan (P) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="jumlah_terpesan_kg" class="form-control" value="{{ old('jumlah_terpesan_kg', $rencana->jumlah_terpesan_kg) }}" required>
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Alokasi Konsumsi / Lain (K) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="jumlah_alokasi_lain_kg" class="form-control" value="{{ old('jumlah_alokasi_lain_kg', $rencana->jumlah_alokasi_lain_kg) }}" required>
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Perkiraan Panen <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_perkiraan_panen" class="form-control" value="{{ old('tanggal_perkiraan_panen', $rencana->tanggal_perkiraan_panen->toDateString()) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Status Rencana</label>
                            <select name="status" class="form-select" required>
                                <option value="aktif" {{ $rencana->status === 'aktif' ? 'selected' : '' }}>Aktif (Pra-Panen)</option>
                                <option value="panen" {{ $rencana->status === 'panen' ? 'selected' : '' }}>Sedang Panen</option>
                                <option value="selesai" {{ $rencana->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ $rencana->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kondisi Tanaman</label>
                            <select name="kondisi_tanaman" class="form-select">
                                <option value="sangat_baik" {{ $rencana->kondisi_tanaman === 'sangat_baik' ? 'selected' : '' }}>Sangat Baik</option>
                                <option value="baik" {{ $rencana->kondisi_tanaman === 'baik' ? 'selected' : '' }}>Baik</option>
                                <option value="sedang" {{ $rencana->kondisi_tanaman === 'sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="terdampak_cuaca" {{ $rencana->kondisi_tanaman === 'terdampak_cuaca' ? 'selected' : '' }}>Terdampak Cuaca</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Grade Hasil</label>
                            <select name="grade" class="form-select">
                                <option value="grade_a" {{ $rencana->grade === 'grade_a' ? 'selected' : '' }}>Grade A</option>
                                <option value="grade_b" {{ $rencana->grade === 'grade_b' ? 'selected' : '' }}>Grade B</option>
                                <option value="campur" {{ $rencana->grade === 'campur' ? 'selected' : '' }}>Campuran</option>
                                <option value="imperfect" {{ $rencana->grade === 'imperfect' ? 'selected' : '' }}>Imperfect</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Fasilitas Penyimpanan</label>
                            <select name="fasilitas_penyimpanan" class="form-select">
                                <option value="open_air" {{ $rencana->fasilitas_penyimpanan === 'open_air' ? 'selected' : '' }}>Terbuka di Lahan</option>
                                <option value="shaded_shed" {{ $rencana->fasilitas_penyimpanan === 'shaded_shed' ? 'selected' : '' }}>Bangsal Berteduh</option>
                                <option value="ventilated_warehouse" {{ $rencana->fasilitas_penyimpanan === 'ventilated_warehouse' ? 'selected' : '' }}>Gudang Berventilasi</option>
                                <option value="cold_storage" {{ $rencana->fasilitas_penyimpanan === 'cold_storage' ? 'selected' : '' }}>Cold Storage</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ekspektasi Harga (Rp/kg)</label>
                            <input type="number" name="ekspektasi_harga_per_kg" class="form-control" value="{{ old('ekspektasi_harga_per_kg', $rencana->ekspektasi_harga_per_kg) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Lokasi Lahan</label>
                            <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi', $rencana->lokasi) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $rencana->catatan) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                        <button type="submit" class="btn btn-pp-primary px-4 fw-semibold">
                            <i class="bi bi-save me-1"></i> Simpan Pembaruan
                        </button>
                        <a href="{{ route('petani.rencana-panen.show', $rencana->id) }}" class="btn btn-light border">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
