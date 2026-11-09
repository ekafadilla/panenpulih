@extends('layouts.app')

@section('title', 'Detail Surplus Panen - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="mb-4 pb-3 border-bottom">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-1">
                <li class="breadcrumb-item"><a href="{{ route('pembeli.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pembeli.jelajah-surplus') }}">Jelajah Surplus</a></li>
                <li class="breadcrumb-item active">{{ $rencana->kode_rencana }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h2 class="fw-bold text-dark mb-0">{{ $rencana->komoditas->nama }}</h2>
            <span class="badge bg-light text-dark border">{{ $rencana->kode_rencana }}</span>
        </div>
        <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i> {{ $rencana->lokasi }}</small>
    </div>

    @php
        $analisis = $rencana->analisisSurplus;
        $risiko = $analisis?->kategori_risiko ?? 'Sedang';
        $badgeClass = match($risiko) {
            'Rendah' => 'badge-risk-rendah',
            'Sedang' => 'badge-risk-sedang',
            'Tinggi' => 'badge-risk-tinggi',
            'Sangat Tinggi' => 'badge-risk-sangat-tinggi',
            default => 'bg-secondary text-white'
        };
        $hariLagi = max(0, (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($rencana->tanggal_perkiraan_panen), false));
    @endphp

    <div class="row g-4">
        <!-- Kolom Kiri: Informasi Surplus & Petani -->
        <div class="col-lg-7">
            <div class="card-custom p-4 bg-white mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-pp-primary px-3 py-1 fw-bold">PROFIL PRE-HARVEST SURPLUS</span>
                    <span class="badge {{ $badgeClass }} px-3 py-1">Risiko: {{ $risiko }}</span>
                </div>

                <div class="p-3 bg-light rounded-4 mb-4">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <small class="text-muted d-block">Potensi Surplus</small>
                            <h4 class="fw-bold text-pp-primary mb-0">{{ number_format($rencana->potensi_surplus_kg, 0) }} kg</h4>
                        </div>
                        <div class="col-4 border-start border-end">
                            <small class="text-muted d-block">Hari Panen</small>
                            <h4 class="fw-bold text-dark mb-0">{{ $hariLagi }} hari lagi</h4>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Grade Produk</small>
                            <h4 class="fw-bold text-dark text-uppercase mb-0">{{ $rencana->grade }}</h4>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-2">Rincian Informasi Lahan & Pascapanen</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered small">
                        <tbody>
                            <tr>
                                <td class="table-light text-muted" style="width: 40%;">Petani / Kelompok Tani</td>
                                <td class="fw-semibold text-dark">{{ $rencana->petani->institution ?? $rencana->petani->name }}</td>
                            </tr>
                            <tr>
                                <td class="table-light text-muted">Tanggal Perkiraan Panen</td>
                                <td>{{ \Carbon\Carbon::parse($rencana->tanggal_perkiraan_panen)->isoFormat('dddd, D MMMM Y') }}</td>
                            </tr>
                            <tr>
                                <td class="table-light text-muted">Kondisi Pertanaman</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $rencana->kondisi_tanaman)) }}</td>
                            </tr>
                            <tr>
                                <td class="table-light text-muted">Fasilitas Penyimpanan Lahan</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $rencana->fasilitas_penyimpanan)) }}</td>
                            </tr>
                            <tr>
                                <td class="table-light text-muted">Daya Simpan Komoditas</td>
                                <td>~{{ $rencana->komoditas->umur_simpan_hari }} hari (Skor Kerentanan: {{ $rencana->komoditas->skor_kerentanan }}/100)</td>
                            </tr>
                            <tr>
                                <td class="table-light text-muted">Ekspektasi Harga Petani</td>
                                <td class="fw-bold text-success">
                                    {{ $rencana->ekspektasi_harga_per_kg ? 'Rp ' . number_format($rencana->ekspektasi_harga_per_kg, 0, ',', '.') . '/kg' : 'Negosiasi Terbuka' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($rencana->catatan)
                    <div class="alert alert-light border small text-muted mb-0">
                        <strong>Catatan Petani:</strong> {{ $rencana->catatan }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Formulir Ajukan Penawaran Pembelian -->
        <div class="col-lg-5">
            <div class="card-custom p-4 bg-white shadow-sm sticky-top" style="top: 80px;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <div class="bg-pp-primary text-white p-2 rounded-3 fs-5">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Ajukan Pembelian Surplus</h5>
                        <small class="text-muted">Kirimkan penawaran harga & kuantitas ke petani</small>
                    </div>
                </div>

                <form action="{{ route('pembeli.penawaran.buat') }}" method="POST">
                    @csrf
                    <input type="hidden" name="rencana_panen_id" value="{{ $rencana->id }}">

                    @if($kebutuhanSaya->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Kaitkan dengan Kebutuhan Saya</label>
                            <select name="kebutuhan_pembeli_id" class="form-select form-select-sm">
                                <option value="">-- Tanpa Kaitan (Pembelian Mandiri) --</option>
                                @foreach($kebutuhanSaya as $kb)
                                    <option value="{{ $kb->id }}">
                                        {{ $kb->kode_kebutuhan }} (Butuh: {{ number_format($kb->jumlah_kg, 0) }} kg &bull; Budget: Rp {{ number_format($kb->budget_per_kg, 0) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jumlah Pembelian yang Diajukan (kg) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="jumlah_kg" id="inputKg" class="form-control" value="{{ min($rencana->potensi_surplus_kg, 300) }}" min="1" max="{{ $rencana->potensi_surplus_kg }}" required>
                            <span class="input-group-text">kg</span>
                        </div>
                        <small class="text-muted">Maksimal surplus tersedia: {{ number_format($rencana->potensi_surplus_kg, 0) }} kg</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Harga Penawaran per kg (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="harga_per_kg" id="inputHarga" class="form-control" value="{{ $rencana->ekspektasi_harga_per_kg ?? 30000 }}" min="100" required>
                        </div>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted fw-semibold">Estimasi Total Transaksi:</span>
                            <h4 class="fw-bold text-pp-primary mb-0" id="totalPreview">Rp 0</h4>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Rencana Tanggal Pengambilan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pengambilan" class="form-control" value="{{ $rencana->tanggal_perkiraan_panen->toDateString() }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Metode Penyaluran / Logistik <span class="text-danger">*</span></label>
                        <select name="metode_pengiriman" class="form-select" required>
                            <option value="diambil_pembeli" selected>Diambil Pembeli Langsung ke Lahan</option>
                            <option value="dikirim_petani">Dikirim Petani ke Titik Usaha</option>
                            <option value="jasa_angkut">Jasa Angkut Bersama Mitra</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pesan / Catatan Tambahan</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Sebutkan kendaraan angkut, jam pengambilan, dll..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-pp-primary btn-lg w-100 fw-bold shadow-sm">
                        <i class="bi bi-send-fill me-1"></i> Kirim Pengajuan ke Petani
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputKg = document.getElementById('inputKg');
        const inputHarga = document.getElementById('inputHarga');
        const totalPreview = document.getElementById('totalPreview');

        function updateTotal() {
            const kg = parseFloat(inputKg.value) || 0;
            const harga = parseFloat(inputHarga.value) || 0;
            const total = kg * harga;
            totalPreview.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        inputKg.addEventListener('input', updateTotal);
        inputHarga.addEventListener('input', updateTotal);

        updateTotal();
    });
</script>
@endpush
