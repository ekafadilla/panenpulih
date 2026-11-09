@extends('layouts.app')

@section('title', 'Tambah Komoditas Baru - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="mb-4 pb-3 border-bottom">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.komoditas.index') }}">Komoditas</a></li>
                <li class="breadcrumb-item active">Tambah Baru</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-0">Tambah Komoditas Pertanian Baru</h2>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom p-4 bg-white">
                <form action="{{ route('admin.komoditas.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Komoditas <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required placeholder="Contoh: Cabai Rawit Merah">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Latin (Botani)</label>
                            <input type="text" name="nama_latin" class="form-control" value="{{ old('nama_latin') }}" placeholder="Contoh: Capsicum frutescens">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select" required>
                                <option value="sayuran">Sayuran</option>
                                <option value="buah">Buah-buahan</option>
                                <option value="palawija">Palawija</option>
                                <option value="rempah">Rempah-rempah</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Daya Simpan Normal (Hari) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="umur_simpan_hari" class="form-control" value="{{ old('umur_simpan_hari', 7) }}" min="1" required>
                                <span class="input-group-text">hari</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Skor Kerentanan U (0-100) <span class="text-danger">*</span></label>
                            <input type="number" name="skor_kerentanan" class="form-control" value="{{ old('skor_kerentanan', 75) }}" min="0" max="100" required>
                            <small class="text-muted">Semakin tinggi = semakin cepat busuk</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Satuan Ukuran <span class="text-danger">*</span></label>
                            <input type="text" name="satuan" class="form-control" value="{{ old('satuan', 'kg') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Harga Acuan per kg (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_acuan_per_kg" class="form-control" value="{{ old('harga_acuan_per_kg', 25000) }}" min="100" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Deskripsi Karakteristik Komoditas</label>
                            <textarea name="deskripsi" class="form-control" rows="2" placeholder="Informasi kadar air, kerentanan transportasi, dll...">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Pedoman Penyimpanan & Pengemasan</label>
                            <textarea name="pedoman_penyimpanan" class="form-control" rows="2" placeholder="Suhu penyimpanan ideal, jenis kemasan peti kayu / keranjang plastik...">{{ old('pedoman_penyimpanan') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <button type="submit" class="btn btn-pp-primary px-4 fw-bold">Simpan Komoditas</button>
                        <a href="{{ route('admin.komoditas.index') }}" class="btn btn-light border">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
