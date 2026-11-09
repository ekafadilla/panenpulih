@extends('layouts.app')

@section('title', 'Input Rencana Panen Baru - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="mb-4 pb-3 border-bottom">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-1">
                <li class="breadcrumb-item"><a href="{{ route('petani.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('petani.rencana-panen.index') }}">Rencana Panen</a></li>
                <li class="breadcrumb-item active">Input Baru</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-0">Input Rencana Panen & Deteksi Surplus</h2>
        <small class="text-muted">Formula Surplus $S = H - P - K$ dan Skor Risiko $R$ akan dihitung otomatis saat Anda mengisi formulir</small>
    </div>

    <form action="{{ route('petani.rencana-panen.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <!-- Formulir Input Kiri -->
            <div class="col-lg-8">
                <div class="card-custom p-4 bg-white mb-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-seedling text-pp-primary me-2"></i> 1. Informasi Komoditas & Waktu Panen</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Pilih Komoditas <span class="text-danger">*</span></label>
                            <select name="komoditas_id" id="inputKomoditas" class="form-select" required>
                                <option value="">-- Pilih Komoditas --</option>
                                @foreach($komoditasList as $k)
                                    <option value="{{ $k->id }}"
                                        data-kerentanan="{{ $k->skor_kerentanan }}"
                                        data-simpan="{{ $k->umur_simpan_hari }}"
                                        data-harga="{{ $k->harga_acuan_per_kg }}"
                                        {{ old('komoditas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama }} (Daya simpan: ~{{ $k->umur_simpan_hari }} hari)
                                    </option>
                                @endforeach
                            </select>
                            <div id="komoditasInfo" class="form-text small text-muted mt-1">
                                Pilih komoditas untuk memuat tingkat kerentanan pascapanen.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Luas Lahan Panen (m²) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="luas_lahan_m2" id="inputLuas" class="form-control" value="{{ old('luas_lahan_m2', 1500) }}" min="10" required>
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Tanam (Opsional)</label>
                            <input type="date" name="tanggal_tanam" class="form-control" value="{{ old('tanggal_tanam') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Perkiraan Panen <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_perkiraan_panen" id="inputTglPanen" class="form-control"
                                   value="{{ old('tanggal_perkiraan_panen', \Carbon\Carbon::now()->addDays(5)->toDateString()) }}" required>
                            <div class="form-text small text-muted" id="hariPanenNotice">
                                Waktu panen menentukan urgensi pencocokan calon pembeli.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-custom p-4 bg-white mb-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-calculator text-pp-primary me-2"></i> 2. Estimasi Produksi & Alokasi Pangan</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Estimasi Hasil Panen (H) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="estimasi_hasil_kg" id="inputH" class="form-control" value="{{ old('estimasi_hasil_kg', 1000) }}" min="1" required>
                                <span class="input-group-text">kg</span>
                            </div>
                            <small class="text-muted d-block mt-1">Total prediksi bobot panen</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Sudah Memiliki Pembeli (P) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="jumlah_terpesan_kg" id="inputP" class="form-control" value="{{ old('jumlah_terpesan_kg', 600) }}" min="0" required>
                                <span class="input-group-text">kg</span>
                            </div>
                            <small class="text-muted d-block mt-1">Pesanan / kontrak langganan</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Alokasi Sendiri / Lain (K) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="jumlah_alokasi_lain_kg" id="inputK" class="form-control" value="{{ old('jumlah_alokasi_lain_kg', 50) }}" min="0" required>
                                <span class="input-group-text">kg</span>
                            </div>
                            <small class="text-muted d-block mt-1">Konsumsi pribadi, benih, kerabat</small>
                        </div>
                    </div>
                </div>

                <div class="card-custom p-4 bg-white mb-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-box-seam text-pp-primary me-2"></i> 3. Kualitas, Penyimpanan & Lokasi Lahan</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kondisi Tanaman Saat Ini <span class="text-danger">*</span></label>
                            <select name="kondisi_tanaman" class="form-select" required>
                                <option value="sangat_baik" selected>Sangat Baik (Bunga & buah lebat)</option>
                                <option value="baik">Baik (Normal tanpa gangguan)</option>
                                <option value="sedang">Sedang (Ada potensi hama ringan)</option>
                                <option value="terdampak_cuaca">Terdampak Cuaca / Hujan Tinggi</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Ekspektasi Grade Hasil <span class="text-danger">*</span></label>
                            <select name="grade" class="form-select" required>
                                <option value="grade_a">Grade A (Super / Ekspor / Swalayan)</option>
                                <option value="grade_b" selected>Grade B (Pasar Tradisional)</option>
                                <option value="campur">Campuran / Belum Disortir</option>
                                <option value="imperfect">Imperfect Produce (Cocok untuk Olahan)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Fasilitas Penyimpanan (G) <span class="text-danger">*</span></label>
                            <select name="fasilitas_penyimpanan" id="inputFasilitas" class="form-select" required>
                                <option value="shaded_shed" selected>Bangsal Sederhana / Berteduh</option>
                                <option value="open_air">Tanpa Penyimpanan (Terbuka di Lahan)</option>
                                <option value="ventilated_warehouse">Gudang Berventilasi Baik</option>
                                <option value="cold_storage">Cold Storage / Ruang Pendingin</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ekspektasi Harga Petani (Rp/kg)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="ekspektasi_harga_per_kg" id="inputHarga" class="form-control" value="{{ old('ekspektasi_harga_per_kg', 30000) }}" min="0">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lokasi / Alamat Lahan <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" id="inputLokasi" class="form-control" value="{{ old('lokasi', $user->address) }}" required>
                        </div>

                        <!-- Interactive Leaflet Map Coordinate Picker -->
                        <div class="col-12">
                            <label class="form-label small fw-bold d-flex justify-content-between">
                                <span>Titik Koordinat Lahan (Pilih di Peta untuk Menghitung Jarak Pembeli)</span>
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Klik pada peta untuk memindahkan pin</small>
                            </label>
                            <div id="mapPicker" class="map-container mb-2"></div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Latitude:</span>
                                        <input type="text" name="latitude" id="inputLat" class="form-control" value="{{ old('latitude', $user->latitude ?? -6.3264) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Longitude:</span>
                                        <input type="text" name="longitude" id="inputLng" class="form-control" value="{{ old('longitude', $user->longitude ?? 108.3200) }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Catatan Tambahan untuk Calon Pembeli</label>
                            <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Petikan pertama buah cabai segar, siap kirim atau ambil langsung ke lokasi kebun...">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Card Kanan (Surplus & Risk Meter) -->
            <div class="col-lg-4">
                <div class="card-custom p-4 bg-white sticky-top shadow-sm" style="top: 80px;">
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cpu text-pp-primary me-1"></i> Surplus Intelligence Preview</h6>
                        <span class="badge bg-success bg-opacity-10 text-success">Live Calc</span>
                    </div>

                    <div class="text-center p-3 rounded-3 bg-light border mb-3">
                        <small class="text-muted d-block fw-semibold">Potensi Surplus (S = H - P - K)</small>
                        <h2 class="fw-extrabold text-pp-primary mb-0" id="previewSurplusKg">350 kg</h2>
                        <small class="text-muted" id="previewSurplusPersen">35% dari estimasi panen</small>
                    </div>

                    <div class="text-center p-3 rounded-3 border mb-3" id="riskBox">
                        <small class="text-muted d-block fw-semibold">Estimasi Indikator Risiko</small>
                        <h3 class="fw-extrabold text-dark mb-1" id="previewRiskScore">68 / 100</h3>
                        <span class="badge badge-risk-tinggi px-3 py-1" id="previewRiskBadge">Risiko: Tinggi</span>
                    </div>

                    <!-- Breakdown Komponen -->
                    <div class="small mb-3">
                        <div class="d-flex justify-content-between text-muted mb-1">
                            <span>0.25 × Proporsi Surplus (Sp):</span>
                            <span class="fw-bold text-dark" id="prevSp">8.75</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted mb-1">
                            <span>0.20 × Waktu Panen (W):</span>
                            <span class="fw-bold text-dark" id="prevW">16.00</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted mb-1">
                            <span>0.25 × Kerentanan (U):</span>
                            <span class="fw-bold text-dark" id="prevU">21.25</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted mb-1">
                            <span>0.15 × Penyimpanan (G):</span>
                            <span class="fw-bold text-dark" id="prevG">9.75</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted">
                            <span>0.15 × Permintaan Pasar (D):</span>
                            <span class="fw-bold text-dark" id="prevD">6.00</span>
                        </div>
                    </div>

                    <div class="alert alert-info small mb-4" id="previewAdvice">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Sistem akan langsung mencocokkan potensi surplus ini dengan seluruh pembeli terdaftar setelah disimpan.
                    </div>

                    <button type="submit" class="btn btn-pp-primary btn-lg w-100 fw-bold shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> Simpan & Cari Pembeli
                    </button>
                    <a href="{{ route('petani.rencana-panen.index') }}" class="btn btn-light border w-100 mt-2 text-muted">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi Map Leaflet untuk Koordinat Picker
        const initLat = parseFloat(document.getElementById('inputLat').value) || -6.3264;
        const initLng = parseFloat(document.getElementById('inputLng').value) || 108.3200;

        const map = L.map('mapPicker').setView([initLat, initLng], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);

        function updateCoords(lat, lng) {
            document.getElementById('inputLat').value = lat.toFixed(6);
            document.getElementById('inputLng').value = lng.toFixed(6);
        }

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });

        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            updateCoords(pos.lat, pos.lng);
        });

        // Live Intelligence Calculation Logic
        const inputKomoditas = document.getElementById('inputKomoditas');
        const inputH = document.getElementById('inputH');
        const inputP = document.getElementById('inputP');
        const inputK = document.getElementById('inputK');
        const inputTglPanen = document.getElementById('inputTglPanen');
        const inputFasilitas = document.getElementById('inputFasilitas');

        function hitungLive() {
            const h = parseFloat(inputH.value) || 0;
            const p = parseFloat(inputP.value) || 0;
            const k = parseFloat(inputK.value) || 0;

            // Surplus S = H - P - K
            const s = Math.max(0, h - p - k);
            const persenSurplus = h > 0 ? (s / h) * 100 : 0;
            const skorSp = Math.min(100, persenSurplus);

            // Hitung hari menuju panen W
            const tglPanenVal = inputTglPanen.value;
            let hariMenujuPanen = 5;
            if (tglPanenVal) {
                const targetDate = new Date(tglPanenVal);
                const today = new Date();
                today.setHours(0,0,0,0);
                const diffTime = targetDate - today;
                hariMenujuPanen = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            }

            let skorW = 10;
            if (hariMenujuPanen <= 0) skorW = 100;
            else if (hariMenujuPanen <= 3) skorW = 95;
            else if (hariMenujuPanen <= 7) skorW = 80;
            else if (hariMenujuPanen <= 14) skorW = 60;
            else if (hariMenujuPanen <= 21) skorW = 40;
            else if (hariMenujuPanen <= 30) skorW = 25;

            // Skor U (Kerentanan Komoditas)
            const selectedOpt = inputKomoditas.options[inputKomoditas.selectedIndex];
            const skorU = selectedOpt && selectedOpt.dataset.kerentanan ? parseFloat(selectedOpt.dataset.kerentanan) : 70;

            // Skor G (Penyimpanan)
            const fas = inputFasilitas.value;
            let skorG = 70;
            if (fas === 'open_air') skorG = 95;
            else if (fas === 'shaded_shed') skorG = 65;
            else if (fas === 'ventilated_warehouse') skorG = 35;
            else if (fas === 'cold_storage') skorG = 10;

            // Skor D estimasi rata-rata
            const skorD = 40;

            // Formula: R = 0.25*Sp + 0.20*W + 0.25*U + 0.15*G + 0.15*D
            const cSp = 0.25 * skorSp;
            const cW = 0.20 * skorW;
            const cU = 0.25 * skorU;
            const cG = 0.15 * skorG;
            const cD = 0.15 * skorD;

            const skorRisiko = Math.min(100, Math.max(0, (cSp + cW + cU + cG + cD))).toFixed(1);

            let kategori = 'Rendah';
            let badgeClass = 'badge-risk-rendah';
            if (skorRisiko >= 75) {
                kategori = 'Sangat Tinggi';
                badgeClass = 'badge-risk-sangat-tinggi';
            } else if (skorRisiko >= 50) {
                kategori = 'Tinggi';
                badgeClass = 'badge-risk-tinggi';
            } else if (skorRisiko >= 25) {
                kategori = 'Sedang';
                badgeClass = 'badge-risk-sedang';
            }

            document.getElementById('previewSurplusKg').textContent = s.toLocaleString('id-ID') + ' kg';
            document.getElementById('previewSurplusPersen').textContent = persenSurplus.toFixed(1) + '% dari estimasi panen';
            document.getElementById('previewRiskScore').textContent = skorRisiko + ' / 100';

            const badge = document.getElementById('previewRiskBadge');
            badge.textContent = 'Risiko: ' + kategori;
            badge.className = 'badge ' + badgeClass + ' px-3 py-1';

            document.getElementById('prevSp').textContent = cSp.toFixed(2);
            document.getElementById('prevW').textContent = cW.toFixed(2);
            document.getElementById('prevU').textContent = cU.toFixed(2);
            document.getElementById('prevG').textContent = cG.toFixed(2);
            document.getElementById('prevD').textContent = cD.toFixed(2);
        }

        [inputKomoditas, inputH, inputP, inputK, inputTglPanen, inputFasilitas].forEach(el => {
            el.addEventListener('input', hitungLive);
            el.addEventListener('change', hitungLive);
        });

        hitungLive();
    });
</script>
@endpush
