@extends('layouts.app')

@section('title', 'Tambah Kebutuhan Komoditas - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="mb-4 pb-3 border-bottom">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-1">
                <li class="breadcrumb-item"><a href="{{ route('pembeli.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pembeli.kebutuhan.index') }}">Kebutuhan</a></li>
                <li class="breadcrumb-item active">Tambah Baru</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-0">Input Kebutuhan Bahan Baku Komoditas</h2>
        <small class="text-muted">Sistem akan otomatis mencocokkan kebutuhan ini dengan rencana panen petani terdekat</small>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom p-4 bg-white">
                <form action="{{ route('pembeli.kebutuhan.store') }}" method="POST">
                    @csrf

                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-basket text-pp-primary me-2"></i> 1. Spesifikasi Komoditas & Volume</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Komoditas yang Dibutuhkan <span class="text-danger">*</span></label>
                            <select name="komoditas_id" class="form-select" required>
                                <option value="">-- Pilih Komoditas --</option>
                                @foreach($komoditasList as $k)
                                    <option value="{{ $k->id }}" {{ old('komoditas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama }} (Kategori: {{ ucfirst($k->kategori) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jumlah Kebutuhan (kg) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="jumlah_kg" class="form-control" value="{{ old('jumlah_kg', 300) }}" min="1" required>
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Mulai Butuh <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai_butuh" class="form-control" value="{{ old('tanggal_mulai_butuh', \Carbon\Carbon::now()->addDays(2)->toDateString()) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Batas Akhir Waktu Kebutuhan <span class="text-danger">*</span></label>
                            <input type="date" name="batas_tanggal_butuh" class="form-control" value="{{ old('batas_tanggal_butuh', \Carbon\Carbon::now()->addDays(10)->toDateString()) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Toleransi Grade Produk <span class="text-danger">*</span></label>
                            <select name="grade_diterima" class="form-select" required>
                                <option value="semua" selected>Semua Grade Diterima</option>
                                <option value="grade_a">Hanya Grade A (Super)</option>
                                <option value="grade_b">Grade B (Standar Pasar)</option>
                                <option value="imperfect">Imperfect Produce (Bentuk Kurang Sempurna/Olahan)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Peruntukan / Tujuan Penggunaan <span class="text-danger">*</span></label>
                            <select name="tujuan_penggunaan" class="form-select" required>
                                <option value="olahan_makanan" {{ $user->role === 'umkm' ? 'selected' : '' }}>Olahan Pangan (Saus, Pasta, Keripik)</option>
                                <option value="pasar_tradisional" {{ $user->role === 'pedagang' ? 'selected' : '' }}>Grosir Pasar Tradisional</option>
                                <option value="supermarket">Pasar Modern / Supermarket</option>
                                <option value="restoran">Hotel & Restoran (HoReCa)</option>
                                <option value="food_bank">Food Bank / Donasi Sosial</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Budget Maksimal (Rp/kg)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="budget_per_kg" class="form-control" value="{{ old('budget_per_kg', 30000) }}" min="0">
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-geo-alt text-pp-primary me-2"></i> 2. Lokasi Gudang / Titik Penerimaan</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Alamat Lokasi Gudang / Titik Bongkar <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi', $user->address) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold d-flex justify-content-between">
                                <span>Pilih Titik Gudang di Peta (Untuk Algoritma Jarak Haversine)</span>
                                <small class="text-muted">Klik pada peta</small>
                            </label>
                            <div id="mapKebutuhan" class="map-container mb-2"></div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Lat:</span>
                                        <input type="text" name="latitude" id="latKebutuhan" class="form-control" value="{{ old('latitude', $user->latitude ?? -6.7120) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Lng:</span>
                                        <input type="text" name="longitude" id="lngKebutuhan" class="form-control" value="{{ old('longitude', $user->longitude ?? 108.5123) }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Catatan Spesifikasi Tambahan</label>
                            <textarea name="spesifikasi" class="form-control" rows="2" placeholder="Contoh: Butuh cabai merah matang segar untuk digiling menjadi pasta sambal. Ukuran tidak seragam tidak masalah...">{{ old('spesifikasi') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <button type="submit" class="btn btn-pp-primary px-4 fw-bold">
                            <i class="bi bi-check-circle me-1"></i> Simpan & Cari Petani Surplus
                        </button>
                        <a href="{{ route('pembeli.kebutuhan.index') }}" class="btn btn-light border">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const initLat = parseFloat(document.getElementById('latKebutuhan').value) || -6.7120;
        const initLng = parseFloat(document.getElementById('lngKebutuhan').value) || 108.5123;

        const map = L.map('mapKebutuhan').setView([initLat, initLng], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            document.getElementById('latKebutuhan').value = e.latlng.lat.toFixed(6);
            document.getElementById('lngKebutuhan').value = e.latlng.lng.toFixed(6);
        });

        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            document.getElementById('latKebutuhan').value = pos.lat.toFixed(6);
            document.getElementById('lngKebutuhan').value = pos.lng.toFixed(6);
        });
    });
</script>
@endpush
