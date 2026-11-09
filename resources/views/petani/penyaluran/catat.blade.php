@extends('layouts.app')

@section('title', 'Catat Realisasi Penyaluran - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="mb-4 pb-3 border-bottom">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-1">
                <li class="breadcrumb-item"><a href="{{ route('petani.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('petani.penawaran.index') }}">Penawaran</a></li>
                <li class="breadcrumb-item active">Catat Penyaluran</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-0">Catat Realisasi Penyaluran Hasil Panen</h2>
        <small class="text-muted">Surplus-to-Impact Tracking: Bandingkan estimasi awal dengan hasil panen dan jumlah terserap riil</small>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom p-4 bg-white mb-4">
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-3">
                    <div>
                        <span class="badge bg-light text-dark border">{{ $penawaran->kode_penawaran }}</span>
                        <h5 class="fw-bold text-dark mb-0 mt-1">Kesepakatan dengan: {{ $penawaran->pembeli->institution ?? $penawaran->pembeli->name }}</h5>
                    </div>
                    <span class="badge bg-success bg-opacity-25 text-success px-3 py-2">Status: Disepakati</span>
                </div>

                <div class="p-3 bg-light rounded-3 mb-4 small">
                    <div class="row g-2">
                        <div class="col-6">
                            <span class="text-muted d-block">Komoditas:</span>
                            <strong class="text-dark">{{ $penawaran->rencanaPanen->komoditas->nama }}</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Estimasi Panen Awal (H):</span>
                            <strong>{{ number_format($penawaran->rencanaPanen->estimasi_hasil_kg, 0) }} kg</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Kuantitas Disepakati:</span>
                            <strong class="text-pp-primary">{{ number_format($penawaran->jumlah_kg, 0) }} kg</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Harga Kesepakatan:</span>
                            <strong class="text-success">Rp {{ number_format($penawaran->harga_per_kg, 0, ',', '.') }}/kg</strong>
                        </div>
                    </div>
                </div>

                <form action="{{ route('petani.penyaluran.simpanCatat', $penawaran->id) }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Realisasi Penyaluran <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_penyaluran" class="form-control" value="{{ old('tanggal_penyaluran', \Carbon\Carbon::now()->toDateString()) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Hasil Panen Aktual (Jika Berbeda dari Estimasi)</label>
                            <div class="input-group">
                                <input type="number" name="hasil_aktual_kg" id="inputHasilAktual" class="form-control" value="{{ old('hasil_aktual_kg', $penawaran->rencanaPanen->estimasi_hasil_kg) }}" min="1">
                                <span class="input-group-text">kg</span>
                            </div>
                            <small class="text-muted">Awalnya diperkirakan {{ number_format($penawaran->rencanaPanen->estimasi_hasil_kg, 0) }} kg</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jumlah Berhasil Disalurkan (kg) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="jumlah_realisasi_kg" id="inputRealisasiKg" class="form-control" value="{{ old('jumlah_realisasi_kg', $penawaran->jumlah_kg) }}" min="1" required>
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Total Pembayaran / Nilai Diterima (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_total_realisasi" id="inputHargaTotal" class="form-control" value="{{ old('harga_total_realisasi', $penawaran->total_harga) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Status Kualitas Saat Serah Terima <span class="text-danger">*</span></label>
                            <select name="status_kualitas" class="form-select" required>
                                <option value="sesuai_ekspektasi" selected>Sesuai Ekspektasi / Segar Sempurna</option>
                                <option value="cacat_ringan">Cacat Ringan (Sedikit lecet tetapi layak konsumsi)</option>
                                <option value="diolah_lanjut">Langsung Masuk Pengolahan (UMKM Pasta/Saus)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Catatan Penyaluran & Logistik</label>
                            <input type="text" name="catatan_penyaluran" class="form-control" placeholder="Nomor plat mobil, kondisi peti kemasan, dll.">
                        </div>
                    </div>

                    <!-- Live Calculation Card: Tingkat Penyerapan -->
                    <div class="p-4 rounded-4 bg-emerald-50 bg-opacity-50 border border-emerald-200 mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-emerald-950"><i class="bi bi-shield-check text-success me-1"></i> Perhitungan Tingkat Penyerapan (Section 11)</span>
                            <span class="badge bg-success" id="badgePenyerapan">85.71%</span>
                        </div>
                        <p class="small text-emerald-800 mb-2">
                            Rumus: <code>(Jumlah Berhasil Disalurkan ÷ Potensi Surplus Aktual) × 100%</code>
                        </p>
                        <div class="d-flex justify-content-between small text-emerald-900 border-top pt-2">
                            <span>Sisa Surplus Belum Terserap:</span>
                            <strong id="sisaSurplusText">50 kg</strong>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-pp-primary px-4 fw-semibold">
                            <i class="bi bi-check-circle me-1"></i> Simpan Realisasi Penyaluran
                        </button>
                        <a href="{{ route('petani.penawaran.index') }}" class="btn btn-light border">Batal</a>
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
        const inputHasilAktual = document.getElementById('inputHasilAktual');
        const inputRealisasiKg = document.getElementById('inputRealisasiKg');
        const inputHargaTotal = document.getElementById('inputHargaTotal');
        const pTerpesan = {{ (float) $penawaran->rencanaPanen->jumlah_terpesan_kg }};
        const pAlokasi = {{ (float) $penawaran->rencanaPanen->jumlah_alokasi_lain_kg }};
        const hargaPerKg = {{ (float) $penawaran->harga_per_kg }};

        function hitungPenyerapan() {
            const hAktual = parseFloat(inputHasilAktual.value) || 0;
            const qRealisasi = parseFloat(inputRealisasiKg.value) || 0;

            // Surplus aktual = H - P - K
            const surplusAktual = Math.max(1, hAktual - pTerpesan - pAlokasi);
            const sisa = Math.max(0, surplusAktual - qRealisasi);

            const rasio = Math.min(100, (qRealisasi / surplusAktual) * 100).toFixed(2);

            document.getElementById('badgePenyerapan').textContent = rasio + '%';
            document.getElementById('sisaSurplusText').textContent = sisa.toLocaleString('id-ID') + ' kg';

            // Auto calculate total price
            if (hargaPerKg > 0 && qRealisasi > 0) {
                inputHargaTotal.value = Math.round(qRealisasi * hargaPerKg);
            }
        }

        inputHasilAktual.addEventListener('input', hitungPenyerapan);
        inputRealisasiKg.addEventListener('input', hitungPenyerapan);

        hitungPenyerapan();
    });
</script>
@endpush
