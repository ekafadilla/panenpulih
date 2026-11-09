@extends('layouts.app')

@section('title', 'Admin Command Center - PANENPULIH')

@section('content')
<div class="container pb-5">
    <!-- Header Admin -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark px-2 py-1">
                    <i class="bi bi-shield-lock me-1"></i> ADMIN COMMAND CENTER
                </span>
                <span class="badge bg-warning text-dark fw-bold">SDG 12 & SDG 2 MONITORING</span>
            </div>
            <h2 class="fw-bold text-dark mt-1 mb-0">National Pre-Harvest Surplus & Food Loss Intelligence</h2>
            <small class="text-muted">Pusat pemantauan risiko surplus, penyerapan pangan, dan metrik dampak ekonomi sirkular</small>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('admin.laporan.sdg') }}" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-file-earmark-bar-graph"></i> Laporan SDG 12 & 2
            </a>
            <a href="{{ route('admin.komoditas.index') }}" class="btn btn-pp-primary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-archive"></i> Master Komoditas
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-people"></i> Pengguna
            </a>
        </div>
    </div>

    <!-- 8 KPI Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Total Petani Terdaftar</small>
                    <div class="bg-light p-2 rounded-2 text-success"><i class="bi bi-flower1"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $totalPetani }}</h3>
                <small class="text-muted">poktan aktif</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Mitra Pembeli (Pedagang & UMKM)</small>
                    <div class="bg-light p-2 rounded-2 text-primary"><i class="bi bi-shop"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $totalPedagang + $totalUmkm }}</h3>
                <small class="text-muted">{{ $totalPedagang }} pedagang &bull; {{ $totalUmkm }} UMKM</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Total Surplus Terdeteksi</small>
                    <div class="bg-light p-2 rounded-2 text-warning"><i class="bi bi-graph-up-arrow"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($metrik['total_surplus_terdeteksi_kg'], 0, ',', '.') }}</h3>
                <small class="text-muted">kg pra-panen</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Tingkat Penyerapan Surplus</small>
                    <div class="bg-light p-2 rounded-2 text-pp-primary"><i class="bi bi-shield-check"></i></div>
                </div>
                <h3 class="fw-bold text-pp-primary mb-0">{{ $metrik['tingkat_penyerapan_persen'] }}%</h3>
                <small class="text-success fw-semibold"><i class="bi bi-arrow-up-right me-1"></i> {{ number_format($metrik['total_tersalurkan_kg'], 0, ',', '.') }} kg terserap</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Food Loss Dicegah (SDG 12.3)</small>
                    <div class="bg-light p-2 rounded-2 text-success"><i class="bi bi-recycle"></i></div>
                </div>
                <h3 class="fw-bold text-success mb-0">{{ $metrik['food_loss_dicegah_ton'] }}</h3>
                <small class="text-muted">metrik ton pangan terselamatkan</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Nilai Ekonomi Terselamatkan</small>
                    <div class="bg-light p-2 rounded-2 text-dark"><i class="bi bi-cash-stack"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0">Rp {{ number_format($metrik['total_nilai_ekonomi_rp'] / 1000000, 1, ',', '.') }} M</h3>
                <small class="text-muted">pendapatan petani diselamatkan</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Estimasi Porsi Pangan (SDG 2)</small>
                    <div class="bg-light p-2 rounded-2 text-info"><i class="bi bi-cup-hot"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($metrik['estimasi_porsi_pangan'], 0, ',', '.') }}</h3>
                <small class="text-muted">porsi makan terselamatkan</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Emisi Gas Rumah Kaca Dicegah</small>
                    <div class="bg-light p-2 rounded-2 text-danger"><i class="bi bi-cloud-slash"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($metrik['estimasi_co2e_dicegah_kg'], 0, ',', '.') }}</h3>
                <small class="text-muted">kg CO₂e avoided</small>
            </div>
        </div>
    </div>

    <!-- Peta Multi-Layer & Visualisasi Grafik -->
    <div class="row g-4 mb-4">
        <!-- Peta Geospasial Distribusi Pertanian & Surplus -->
        <div class="col-lg-8">
            <div class="card-custom p-4 bg-white mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-geo-alt-fill text-danger me-2"></i> Peta Distribusi Geospasial Petani & Pembeli</h5>
                        <small class="text-muted">Pantau titik kebun surplus panen dan jejaring pedagang/UMKM</small>
                    </div>
                    <span class="badge bg-light text-dark border">{{ count($petaPins) }} Entitas Terdaftar</span>
                </div>
                <div id="mapAdmin" class="map-container mb-2"></div>
                <div class="d-flex align-items-center gap-3 small text-muted mt-2">
                    <span><i class="bi bi-circle-fill text-success me-1"></i> Rencana Panen Petani (Surplus)</span>
                    <span><i class="bi bi-circle-fill text-primary me-1"></i> Pedagang Grosir</span>
                    <span><i class="bi bi-circle-fill text-warning me-1"></i> UMKM Pengolah Pangan</span>
                </div>
            </div>

            <!-- Grafik Surplus per Komoditas -->
            <div class="card-custom p-4 bg-white">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-bar-chart-fill text-pp-primary me-2"></i> Potensi Surplus Berdasarkan Komoditas (kg)</h5>
                <canvas id="chartKomoditas" height="120"></canvas>
            </div>
        </div>

        <!-- Kolom Kanan: Distribusi Risiko & Realisasi Penyaluran -->
        <div class="col-lg-4">
            <!-- Doughnut Chart: Distribusi Kategori Risiko Pre-Harvest -->
            <div class="card-custom p-4 bg-white mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart-fill text-warning me-2"></i> Distribusi Risiko Surplus Pre-Harvest</h6>
                <canvas id="chartRisiko" height="200"></canvas>
                <div class="mt-3 small border-top pt-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="bi bi-circle-fill text-danger me-1"></i> Sangat Tinggi (75-100):</span>
                        <strong class="text-danger">{{ $metrik['distribusi_risiko']['Sangat Tinggi'] }} rencana</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="bi bi-circle-fill text-warning me-1"></i> Tinggi (50-74):</span>
                        <strong class="text-warning">{{ $metrik['distribusi_risiko']['Tinggi'] }} rencana</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="bi bi-circle-fill text-primary me-1"></i> Sedang (25-49):</span>
                        <strong class="text-primary">{{ $metrik['distribusi_risiko']['Sedang'] }} rencana</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-circle-fill text-success me-1"></i> Rendah (0-24):</span>
                        <strong class="text-success">{{ $metrik['distribusi_risiko']['Rendah'] }} rencana</strong>
                    </div>
                </div>
            </div>

            <!-- Transaksi Penyaluran Terakhir -->
            <div class="card-custom p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-pp-primary me-2"></i> Penyaluran Terbaru</h6>
                    <a href="{{ route('admin.laporan.sdg') }}" class="small text-pp-primary text-decoration-none">Detail</a>
                </div>

                @if($penyaluranTerbaru->isEmpty())
                    <p class="text-muted small mb-0">Belum ada catatan penyaluran.</p>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($penyaluranTerbaru as $peny)
                            <div class="p-2 bg-light rounded border small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="badge bg-white text-dark border">{{ $peny->kode_penyaluran }}</span>
                                    <span class="badge bg-success bg-opacity-25 text-success">{{ $peny->tingkat_penyerapan_persen }}% terserap</span>
                                </div>
                                <div class="fw-bold text-dark">{{ $peny->penawaran->rencanaPanen->komoditas->nama }} &bull; {{ number_format($peny->jumlah_realisasi_kg, 0) }} kg</div>
                                <div class="text-muted d-flex justify-content-between" style="font-size: 0.75rem;">
                                    <span>{{ $peny->penawaran->petani->name }} &rarr; {{ $peny->penawaran->pembeli->name }}</span>
                                    <span class="fw-bold text-dark">Rp {{ number_format($peny->harga_total_realisasi, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Peta Admin Geospasial
        const pins = @json($petaPins);
        const map = L.map('mapAdmin').setView([-6.6, 107.9], 8);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const bounds = [];
        pins.forEach(pin => {
            if (pin.lat && pin.lng) {
                let iconColor = 'green';
                if (pin.tipe === 'pedagang') iconColor = 'blue';
                else if (pin.tipe === 'umkm') iconColor = 'orange';

                const marker = L.marker([pin.lat, pin.lng], {
                    icon: L.icon({
                        iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-${iconColor}.png`,
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map);

                marker.bindPopup(`
                    <strong>${pin.judul}</strong><br>
                    Lokasi: ${pin.lokasi}<br>
                    ${pin.surplus_kg > 0 ? `Surplus: <strong>${pin.surplus_kg} kg</strong><br>Tgl Panen: ${pin.tgl_panen}<br>Tingkat Risiko: <strong>${pin.risiko}</strong>` : `Tipe: <strong>${pin.tipe.toUpperCase()}</strong>`}
                `);

                bounds.push([pin.lat, pin.lng]);
            }
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [40, 40] });
        }

        // 2. Bar Chart Surplus Komoditas
        const komoditasData = @json($komoditasSurplus);
        const labelsKomoditas = komoditasData.map(item => item.nama);
        const valuesKomoditas = komoditasData.map(item => parseFloat(item.total_surplus) || 0);

        new Chart(document.getElementById('chartKomoditas'), {
            type: 'bar',
            data: {
                labels: labelsKomoditas,
                datasets: [{
                    label: 'Potensi Surplus (kg)',
                    data: valuesKomoditas,
                    backgroundColor: '#059669',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(val) { return val + ' kg'; }
                        }
                    }
                }
            }
        });

        // 3. Doughnut Chart Distribusi Risiko
        const distRisiko = @json($metrik['distribusi_risiko']);
        new Chart(document.getElementById('chartRisiko'), {
            type: 'doughnut',
            data: {
                labels: ['Rendah', 'Sedang', 'Tinggi', 'Sangat Tinggi'],
                datasets: [{
                    data: [
                        distRisiko['Rendah'] || 0,
                        distRisiko['Sedang'] || 0,
                        distRisiko['Tinggi'] || 0,
                        distRisiko['Sangat Tinggi'] || 0,
                    ],
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endpush
