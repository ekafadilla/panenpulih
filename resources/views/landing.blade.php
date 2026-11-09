@extends('layouts.app')

@section('title', 'PANENPULIH - Smart Pre-Harvest Surplus Intelligence & Distribution Platform')

@section('content')
<div class="container pb-5">

    <!-- HERO SECTION -->
    <div class="row align-items-center g-5 py-4 py-lg-5">
        <div class="col-lg-7">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-emerald-100 text-emerald-800 border border-emerald-300 small fw-bold mb-3" style="background-color: #d1fae5; color: #065f46; border-color: #a7f3d0;">
                <i class="bi bi-shield-check"></i> PRE-HARVEST SURPLUS INTELLIGENCE 2026
            </div>
            <h1 class="display-5 fw-extrabold text-dark tracking-tight mb-3" style="letter-spacing: -1px; font-weight: 800;">
                Mengantisipasi <span class="text-pp-primary">Surplus Panen</span>, Menyelamatkan Hasil Pertanian Sebelum Terbuang.
            </h1>
            <p class="lead text-secondary mb-4" style="line-height: 1.6;">
                PANENPULIH bukan sekadar marketplace, melainkan platform berbasis kecerdasan data yang menghitung potensi kelebihan panen <span class="fw-semibold text-dark">sebelum masa panen tiba</span>, menilai indikasi risiko pembusukan, dan secara presisi mencocokkan pasokan dengan pedagang serta UMKM pengolah pangan.
            </p>

            <div class="d-flex flex-wrap gap-3 mb-4">
                <a href="#kalkulator" class="btn btn-pp-primary btn-lg px-4 py-2 d-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-calculator"></i> Coba Simulator Algoritma
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4 py-2 d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk Platform
                </a>
            </div>

            <!-- Fast Role Switcher Box -->
            <div class="p-3 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center gap-2 small fw-bold text-dark mb-2">
                    <i class="bi bi-play-circle-fill text-pp-primary"></i> Akses Cepat Evaluasi Multi-Peran (1-Click Login):
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('auth.demo', 'petani') }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-flower1 me-1"></i> Petani (Pak Supardi)
                    </a>
                    <a href="{{ route('auth.demo', 'pedagang') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-truck me-1"></i> Pedagang (H. Hendra)
                    </a>
                    <a href="{{ route('auth.demo', 'umkm') }}" class="btn btn-sm btn-outline-warning text-dark">
                        <i class="bi bi-shop me-1"></i> UMKM (Bu Broto)
                    </a>
                    <a href="{{ route('auth.demo', 'admin') }}" class="btn btn-sm btn-outline-dark">
                        <i class="bi bi-shield-check me-1"></i> Admin Bappenas
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <!-- Impact Card Box -->
            <div class="card-custom p-4 border-0 shadow-lg position-relative overflow-hidden" style="background: linear-gradient(145deg, #ffffff 0%, #f0fdf4 100%);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-pp-primary px-3 py-2 fw-bold">CAPAIAN PLATFORM 2026</span>
                    <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-award me-1"></i> SDG 12 & 2</span>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 bg-white rounded-3 border">
                            <small class="text-muted d-block fw-semibold">Surplus Terdeteksi</small>
                            <span class="fs-4 fw-bold text-dark">{{ number_format($metrik['total_surplus_terdeteksi_kg'], 0, ',', '.') }}</span>
                            <span class="small text-muted">kg</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-white rounded-3 border">
                            <small class="text-muted d-block fw-semibold">Pangan Terserap</small>
                            <span class="fs-4 fw-bold text-pp-primary">{{ number_format($metrik['total_tersalurkan_kg'], 0, ',', '.') }}</span>
                            <span class="small text-muted">kg</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-white rounded-3 border">
                            <small class="text-muted d-block fw-semibold">Tingkat Penyerapan</small>
                            <span class="fs-4 fw-bold text-emerald-600">{{ $metrik['tingkat_penyerapan_persen'] }}%</span>
                            <span class="badge bg-light text-success border ms-1">Efektif</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-white rounded-3 border">
                            <small class="text-muted d-block fw-semibold">Nilai Ekonomi</small>
                            <span class="fs-5 fw-bold text-dark">Rp {{ number_format($metrik['total_nilai_ekonomi_rp'] / 1000000, 1, ',', '.') }}</span>
                            <span class="small text-muted">Juta</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between small text-muted">
                        <span><i class="bi bi-check2-circle text-success me-1"></i> Emisi CO2 Dicegah:</span>
                        <strong class="text-dark">{{ number_format($metrik['estimasi_co2e_dicegah_kg'], 0, ',', '.') }} kg CO₂e</strong>
                    </div>
                    <div class="d-flex align-items-center justify-content-between small text-muted mt-1">
                        <span><i class="bi bi-cup-hot text-primary me-1"></i> Estimasi Porsi Diselamatkan:</span>
                        <strong class="text-dark">{{ number_format($metrik['estimasi_porsi_pangan'], 0, ',', '.') }} porsi</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3 NOVELTY PILLARS -->
    <div class="row g-4 my-4">
        <div class="col-12 text-center mb-2">
            <span class="badge bg-light text-pp-primary border px-3 py-1 fw-bold">NOVELTY & VALUE PROPOSITION</span>
            <h3 class="fw-bold mt-2">Tiga Pendekatan Utama PANENPULIH</h3>
            <p class="text-muted mx-auto" style="max-width: 680px;">
                Mengatasi gap riset dengan beralih dari penanganan surplus pascapanen (reaktif) menjadi pendeteksian cerdas pra-panen (proaktif).
            </p>
        </div>

        <div class="col-md-4">
            <div class="card-custom h-100 p-4 border-top border-4 border-success">
                <div class="d-inline-flex p-3 rounded-4 bg-success bg-opacity-10 text-success mb-3 fs-3">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <h5 class="fw-bold mb-2">1. Pre-Harvest Surplus Detection</h5>
                <p class="text-muted small mb-3">
                    Petani memasukkan perkiraan panen ($H$), komitmen terpesan ($P$), dan alokasi konsumsi ($K$). Sistem secara otomatis menghitung potensi surplus $S = H - P - K$ sebelum panen berlangsung.
                </p>
                <div class="p-2 bg-light rounded text-center small fw-semibold text-dark border">
                    Rumus: <code>S = H - P - K</code>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-custom h-100 p-4 border-top border-4 border-warning">
                <div class="d-inline-flex p-3 rounded-4 bg-warning bg-opacity-10 text-warning mb-3 fs-3">
                    <i class="bi bi-bullseye"></i>
                </div>
                <h5 class="fw-bold mb-2">2. Risk-Based Buyer Matching</h5>
                <p class="text-muted small mb-3">
                    Mencocokkan surplus dengan pedagang dan UMKM berdasarkan formula multi-kriteria: komoditas ($C$), kuantitas ($Q$), jarak lokasi ($L$), jendela waktu ($T$), dan kesesuaian kualitas grade ($J$).
                </p>
                <div class="p-2 bg-light rounded text-center small fw-semibold text-dark border">
                    <code>M = 0.30C + 0.20Q + 0.20L + 0.20T + 0.10J</code>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-custom h-100 p-4 border-top border-4 border-primary">
                <div class="d-inline-flex p-3 rounded-4 bg-primary bg-opacity-10 text-primary mb-3 fs-3">
                    <i class="bi bi-clipboard2-data"></i>
                </div>
                <h5 class="fw-bold mb-2">3. Surplus-to-Impact Tracking</h5>
                <p class="text-muted small mb-3">
                    Mencatat realisasi hasil panen yang berhasil disalurkan, sisa surplus, dan rasio penyerapan pangan riil untuk mengukur dampak konkret terhadap pencegahan food loss (SDG 12.3 & SDG 2).
                </p>
                <div class="p-2 bg-light rounded text-center small fw-semibold text-dark border">
                    <code>Penyerapan = (Realisasi ÷ S_aktual) × 100%</code>
                </div>
            </div>
        </div>
    </div>

    <!-- LIVE INTERACTIVE CALCULATOR (Novelty 1 & 2 Prototype) -->
    <div id="kalkulator" class="card-custom p-4 p-lg-5 my-5 border-0 shadow-lg" style="background: #ffffff;">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-warning bg-opacity-25 text-dark small fw-bold mb-2">
                    <i class="bi bi-lightning-charge-fill text-warning"></i> LIVE SIMULATOR ALGORITMA
                </div>
                <h3 class="fw-bold text-dark mb-2">Simulasi Deteksi Surplus & Penilaian Risiko</h3>
                <p class="text-muted small mb-4">
                    Uji coba langsung formula matematis PANENPULIH. Masukkan data simulasi panen Anda di bawah ini dan perhatikan bagaimana potensi surplus dan skor risiko dihitung secara otomatis.
                </p>

                <form id="formSimulasi">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Estimasi Panen (H)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" id="simH" class="form-control" value="1000" min="1">
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Sudah Terpesan (P)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" id="simP" class="form-control" value="600" min="0">
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Alokasi Lain (K)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" id="simK" class="form-control" value="50" min="0">
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Hari Menuju Panen (W)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" id="simW" class="form-control" value="5" min="1" max="90">
                                <span class="input-group-text">hari</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kerentanan Komoditas (U)</label>
                            <select id="simU" class="form-select form-select-sm">
                                <option value="85" selected>Cabai Merah (Skor: 85 - Tinggi)</option>
                                <option value="88">Tomat Sayur (Skor: 88 - Sangat Tinggi)</option>
                                <option value="40">Bawang Merah (Skor: 40 - Sedang)</option>
                                <option value="75">Manggis (Skor: 75 - Tinggi)</option>
                                <option value="25">Kentang (Skor: 25 - Rendah)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Fasilitas Penyimpanan (G)</label>
                            <select id="simG" class="form-select form-select-sm">
                                <option value="shaded_shed" selected>Bangsal Sederhana / Berteduh</option>
                                <option value="open_air">Tanpa Penyimpanan (Terbuka)</option>
                                <option value="ventilated_warehouse">Gudang Berventilasi Baik</option>
                                <option value="cold_storage">Cold Storage Pendingin</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Permintaan Terdaftar (D)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" id="simD" class="form-control" value="400" min="0">
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-6">
                <!-- Result Box -->
                <div class="p-4 rounded-4 border bg-light text-center">
                    <h6 class="text-muted fw-bold text-uppercase small mb-3">Hasil Evaluasi Surplus & Risiko Realtime</h6>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="bg-white p-3 rounded-3 border">
                                <small class="text-muted d-block">Potensi Surplus (S)</small>
                                <h3 class="fw-extrabold text-pp-primary mb-0" id="resSurplusKg">350 kg</h3>
                                <small class="text-muted" id="resPersenSurplus">35% dari total panen</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-white p-3 rounded-3 border">
                                <small class="text-muted d-block">Skor Risiko (R)</small>
                                <h3 class="fw-extrabold text-dark mb-0" id="resSkorRisiko">68 / 100</h3>
                                <span class="badge badge-risk-tinggi px-2 py-1 mt-1" id="resKategoriRisiko">Tinggi</span>
                            </div>
                        </div>
                    </div>

                    <!-- Formula Component Breakdown -->
                    <div class="p-3 bg-white rounded-3 border text-start small mb-3">
                        <div class="fw-bold mb-2 text-dark"><i class="bi bi-pie-chart me-1"></i> Rincian 5 Komponen Indikator:</div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>0.25 × Proporsi Surplus (Sp):</span>
                            <span class="fw-bold" id="resKomponenSp">8.75</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>0.20 × Kedekatan Waktu (W):</span>
                            <span class="fw-bold" id="resKomponenW">16.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>0.25 × Kerentanan Komoditas (U):</span>
                            <span class="fw-bold" id="resKomponenU">21.25</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>0.15 × Fasilitas Penyimpanan (G):</span>
                            <span class="fw-bold" id="resKomponenG">9.75</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>0.15 × Keseimbangan Pasar (D):</span>
                            <span class="fw-bold" id="resKomponenD">5.25</span>
                        </div>
                    </div>

                    <div class="alert alert-info text-start small mb-0" id="resRekomendasiMitigasi">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <strong>Rekomendasi Awal:</strong> Segera prioritaskan penyaluran ke pedagang & UMKM terdekat sebelum waktu panen (H-5) agar kualitas produk tetap prima.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PRE-HARVEST SURPLUS FEED -->
    <div class="my-5">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
            <div>
                <span class="badge bg-light text-pp-primary border px-3 py-1 fw-bold">KATALOG PRE-HARVEST</span>
                <h3 class="fw-bold mt-2">Daftar Panen dengan Potensi Surplus Aktif</h3>
                <p class="text-muted small mb-0">Informasi panen yang sedang dihitung dan menunggu pencocokan calon pembeli.</p>
            </div>
            <a href="{{ route('login') }}" class="btn btn-outline-success btn-sm mt-2 mt-md-0">
                Lihat Semua Surplus di Platform <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($surplusTerbaru as $rp)
                @php
                    $hariLagi = max(0, (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($rp->tanggal_perkiraan_panen), false));
                    $risiko = $rp->analisisSurplus?->kategori_risiko ?? 'Sedang';
                    $badgeClass = match($risiko) {
                        'Rendah' => 'badge-risk-rendah',
                        'Sedang' => 'badge-risk-sedang',
                        'Tinggi' => 'badge-risk-tinggi',
                        'Sangat Tinggi' => 'badge-risk-sangat-tinggi',
                        default => 'bg-secondary text-white'
                    };
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card-custom h-100 p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-light text-dark border">{{ $rp->kode_rencana }}</span>
                                <span class="badge {{ $badgeClass }} px-2 py-1">Risiko: {{ $risiko }}</span>
                            </div>

                            <h5 class="fw-bold mb-1 text-dark">{{ $rp->komoditas->nama }}</h5>
                            <p class="text-muted small mb-2"><i class="bi bi-geo-alt text-danger me-1"></i> {{ $rp->lokasi }}</p>

                            <div class="p-2 bg-light rounded-3 mb-3 small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Estimasi Panen (H):</span>
                                    <strong class="text-dark">{{ number_format($rp->estimasi_hasil_kg, 0) }} kg</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Sudah Terpesan (P):</span>
                                    <strong class="text-muted">{{ number_format($rp->jumlah_terpesan_kg, 0) }} kg</strong>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-1 text-emerald-700 fw-bold">
                                    <span>Potensi Surplus (S):</span>
                                    <span class="text-pp-primary fs-6">{{ number_format($rp->potensi_surplus_kg, 0) }} kg</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between align-items-center small mb-3 text-muted">
                                <span><i class="bi bi-clock me-1"></i> {{ $hariLagi }} hari lagi</span>
                                <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($rp->tanggal_perkiraan_panen)->isoFormat('D MMM Y') }}</span>
                            </div>

                            <a href="{{ route('auth.demo', 'pedagang') }}" class="btn btn-sm btn-outline-primary w-100 fw-semibold">
                                <i class="bi bi-eye me-1"></i> Lihat & Ajukan Penawaran
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Belum ada data rencana panen aktif.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- RESEARCH GAP & LITERATURE SYNTHESIS (Section 2 & 4) -->
    <div class="card-custom p-4 p-lg-5 my-5 border-0 shadow-sm">
        <div class="text-center mb-4">
            <span class="badge bg-emerald-100 text-emerald-800 border px-3 py-1 fw-bold">KAJIAN ILMIAH & RESEARCH GAP</span>
            <h3 class="fw-bold mt-2">Landasan Studi Literatur & Kontribusi PANENPULIH</h3>
            <p class="text-muted mx-auto" style="max-width: 720px;">
                Berdasarkan sintesis artikel jurnal bereputasi terkait food loss, circular economy, dan platform berbagi pangan.
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle small border">
                <thead class="table-light">
                    <tr>
                        <th style="width: 28%;">Penelitian & Penulis</th>
                        <th style="width: 25%;">Fokus Literatur</th>
                        <th style="width: 35%;">Kontribusi & Relevansi untuk PANENPULIH</th>
                        <th style="width: 12%;">Tautan DOI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($literatur as $lit)
                        <tr>
                            <td>
                                <strong class="d-block text-dark">{{ $lit['judul'] }}</strong>
                                <small class="text-muted">{{ $lit['penulis'] }}</small>
                                <span class="badge bg-light text-secondary border mt-1">{{ $lit['jurnal'] }}</span>
                            </td>
                            <td class="text-secondary">{{ $lit['fokus'] }}</td>
                            <td class="text-emerald-900 fw-medium bg-emerald-50 bg-opacity-25">{{ $lit['kontribusi'] }}</td>
                            <td>
                                <a href="{{ $lit['doi'] }}" target="_blank" class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size: 0.72rem;">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka DOI
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const simH = document.getElementById('simH');
        const simP = document.getElementById('simP');
        const simK = document.getElementById('simK');
        const simW = document.getElementById('simW');
        const simU = document.getElementById('simU');
        const simG = document.getElementById('simG');
        const simD = document.getElementById('simD');

        function updateSimulasi() {
            const h = parseFloat(simH.value) || 0;
            const p = parseFloat(simP.value) || 0;
            const k = parseFloat(simK.value) || 0;
            const w = parseInt(simW.value) || 0;
            const u = parseFloat(simU.value) || 70;
            const gType = simG.value;
            const dKg = parseFloat(simD.value) || 0;

            // 1. S = H - P - K
            const s = Math.max(0, h - p - k);
            const persenSurplus = h > 0 ? (s / h) * 100 : 0;
            const skorSp = Math.min(100, persenSurplus);

            // 2. Skor W
            let skorW = 10;
            if (w <= 0) skorW = 100;
            else if (w <= 3) skorW = 95;
            else if (w <= 7) skorW = 80;
            else if (w <= 14) skorW = 60;
            else if (w <= 21) skorW = 40;
            else if (w <= 30) skorW = 25;

            // 3. Skor U
            const skorU = u;

            // 4. Skor G
            let skorG = 70;
            if (gType === 'open_air') skorG = 95;
            else if (gType === 'shaded_shed') skorG = 65;
            else if (gType === 'ventilated_warehouse') skorG = 35;
            else if (gType === 'cold_storage') skorG = 10;

            // 5. Skor D
            const rasioD = s > 0 ? (dKg / s) : 2.0;
            let skorD = 85;
            if (rasioD >= 2.0) skorD = 15;
            else if (rasioD >= 1.0) skorD = 35;
            else if (rasioD >= 0.5) skorD = 65;

            // R = 0.25Sp + 0.20W + 0.25U + 0.15G + 0.15D
            const compSp = 0.25 * skorSp;
            const compW = 0.20 * skorW;
            const compU = 0.25 * skorU;
            const compG = 0.15 * skorG;
            const compD = 0.15 * skorD;

            const skorRisiko = Math.min(100, Math.max(0, (compSp + compW + compU + compG + compD))).toFixed(1);

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

            document.getElementById('resSurplusKg').textContent = s.toLocaleString('id-ID') + ' kg';
            document.getElementById('resPersenSurplus').textContent = persenSurplus.toFixed(1) + '% dari total panen';
            document.getElementById('resSkorRisiko').textContent = skorRisiko + ' / 100';

            const badgeElem = document.getElementById('resKategoriRisiko');
            badgeElem.textContent = kategori;
            badgeElem.className = 'badge ' + badgeClass + ' px-2 py-1 mt-1';

            document.getElementById('resKomponenSp').textContent = compSp.toFixed(2);
            document.getElementById('resKomponenW').textContent = compW.toFixed(2);
            document.getElementById('resKomponenU').textContent = compU.toFixed(2);
            document.getElementById('resKomponenG').textContent = compG.toFixed(2);
            document.getElementById('resKomponenD').textContent = compD.toFixed(2);
        }

        [simH, simP, simK, simW, simU, simG, simD].forEach(input => {
            input.addEventListener('input', updateSimulasi);
            input.addEventListener('change', updateSimulasi);
        });

        updateSimulasi();
    });
</script>
@endpush
