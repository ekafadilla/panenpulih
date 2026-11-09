@extends('layouts.app')

@section('title', 'Dashboard Pembeli - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge {{ $user->role === 'umkm' ? 'bg-warning text-dark' : 'bg-primary text-white' }} px-2 py-1">
                    <i class="bi bi-{{ $user->role === 'umkm' ? 'shop' : 'truck' }} me-1"></i> PANEL {{ strtoupper($user->role) }}
                </span>
                <span class="text-muted small">{{ $user->institution ?? $user->name }}</span>
            </div>
            <h2 class="fw-bold text-dark mt-1 mb-0">Selamat Datang, {{ $user->name }}!</h2>
            <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i> {{ $user->address }}</small>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('pembeli.kebutuhan.create') }}" class="btn btn-pp-primary d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-plus-circle"></i> Input Kebutuhan Bahan Baku
            </a>
            <a href="{{ route('pembeli.jelajah-surplus') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <i class="bi bi-search"></i> Jelajah Surplus Panen
            </a>
        </div>
    </div>

    <!-- Metrik Pembeli -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Kebutuhan Aktif</small>
                    <div class="bg-light p-2 rounded-2 text-dark"><i class="bi bi-basket"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $kebutuhanAktif->count() }}</h3>
                <small class="text-muted">komoditas dicari</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Total Kuantitas Dicari</small>
                    <div class="bg-light p-2 rounded-2 text-primary"><i class="bi bi-box-seam"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($totalKgDibutuhkan, 0, ',', '.') }}</h3>
                <small class="text-muted">kg bahan baku</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Surplus Cocok Terdekat</small>
                    <div class="bg-light p-2 rounded-2 text-warning"><i class="bi bi-bullseye"></i></div>
                </div>
                <h3 class="fw-bold text-pp-primary mb-0">{{ $surplusCocok->count() }}</h3>
                <small class="text-muted">rencana panen tersedia</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Pangan Berhasil Diterima</small>
                    <div class="bg-light p-2 rounded-2 text-success"><i class="bi bi-truck"></i></div>
                </div>
                <h3 class="fw-bold text-success mb-0">{{ number_format($totalKgDiterima, 0, ',', '.') }}</h3>
                <small class="text-muted">kg terserap usaha Anda</small>
            </div>
        </div>
    </div>

    <!-- Peta Interaktif & Surplus Cocok -->
    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Peta Sebaran Surplus Pre-Harvest Terdekat -->
            <div class="card-custom p-4 bg-white mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-map text-pp-primary me-2"></i> Peta Sebaran Panen & Surplus Terdekat</h5>
                        <small class="text-muted">Visualisasi lokasi kebun petani dan gudang Anda</small>
                    </div>
                    <a href="{{ route('pembeli.jelajah-surplus') }}" class="btn btn-sm btn-outline-secondary">
                        Filter Lengkap
                    </a>
                </div>
                <div id="petaPembeli" class="map-container mb-2"></div>
                <div class="d-flex align-items-center gap-3 small text-muted mt-2">
                    <span><i class="bi bi-circle-fill text-success me-1"></i> Lokasi Petani (Surplus)</span>
                    <span><i class="bi bi-circle-fill text-primary me-1"></i> Lokasi Usaha Anda</span>
                </div>
            </div>

            <!-- Rekomendasi Surplus Pre-Harvest yang Cocok -->
            <div class="card-custom p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-stars text-warning me-2"></i> Panen Menjelang Petik (Cocok untuk Usaha Anda)</h5>
                    <a href="{{ route('pembeli.jelajah-surplus') }}" class="small text-pp-primary text-decoration-none">Lihat Semua</a>
                </div>

                @if($surplusCocok->isEmpty())
                    <p class="text-muted small mb-0">Belum ada surplus yang cocok. Coba tambahkan komoditas di menu Kebutuhan Saya.</p>
                @else
                    <div class="row g-3">
                        @foreach($surplusCocok as $sc)
                            @php
                                $hariLagi = max(0, (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($sc->tanggal_perkiraan_panen), false));
                                $risiko = $sc->analisisSurplus?->kategori_risiko ?? 'Sedang';
                                $badgeClass = match($risiko) {
                                    'Rendah' => 'badge-risk-rendah',
                                    'Sedang' => 'badge-risk-sedang',
                                    'Tinggi' => 'badge-risk-tinggi',
                                    'Sangat Tinggi' => 'badge-risk-sangat-tinggi',
                                    default => 'bg-secondary text-white'
                                };
                            @endphp
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge bg-white text-dark border">{{ $sc->kode_rencana }}</span>
                                            <span class="badge {{ $badgeClass }} px-2 py-1">Risiko: {{ $risiko }}</span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $sc->komoditas->nama }}</h6>
                                        <small class="text-muted d-block mb-2">
                                            <i class="bi bi-flower1 text-success me-1"></i> {{ $sc->petani->name }} &bull; {{ $sc->lokasi }}
                                        </small>

                                        <div class="bg-white p-2 rounded border small mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span>Potensi Surplus:</span>
                                                <strong class="text-pp-primary fs-6">{{ number_format($sc->potensi_surplus_kg, 0) }} kg</strong>
                                            </div>
                                            <div class="d-flex justify-content-between text-muted">
                                                <span>Waktu Panen:</span>
                                                <span>{{ \Carbon\Carbon::parse($sc->tanggal_perkiraan_panen)->isoFormat('D MMM Y') }} ({{ $hariLagi }} hari)</span>
                                            </div>
                                            <div class="d-flex justify-content-between text-muted">
                                                <span>Toleransi Grade:</span>
                                                <span class="text-uppercase fw-semibold">{{ $sc->grade }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ route('pembeli.jelajah-surplus.detail', $sc->id) }}" class="btn btn-sm btn-outline-primary w-100 fw-semibold">
                                        <i class="bi bi-cart-plus me-1"></i> Lihat & Ajukan Pembelian
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Kebutuhan Aktif & Penawaran Berjalan -->
        <div class="col-lg-4">
            <!-- Kebutuhan Saya -->
            <div class="card-custom p-4 bg-white mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-basket text-primary me-2"></i> Kebutuhan Bahan Baku Aktif</h6>
                    <a href="{{ route('pembeli.kebutuhan.create') }}" class="btn btn-xs btn-pp-primary py-1 px-2">+ Tambah</a>
                </div>

                @if($kebutuhanAktif->isEmpty())
                    <p class="text-muted small mb-0">Belum ada kebutuhan terdaftar. Klik "+ Tambah" untuk mendaftarkan bahan baku yang Anda cari.</p>
                @else
                    <div class="d-flex flex-column gap-2">
                        @foreach($kebutuhanAktif as $kbt)
                            <div class="p-2 bg-light rounded border small">
                                <div class="d-flex justify-content-between fw-bold text-dark">
                                    <span>{{ $kbt->komoditas->nama }}</span>
                                    <span class="text-pp-primary">{{ number_format($kbt->jumlah_kg, 0) }} kg</span>
                                </div>
                                <div class="text-muted d-flex justify-content-between mt-1" style="font-size: 0.75rem;">
                                    <span>Grade: {{ ucfirst($kbt->grade_diterima) }}</span>
                                    <span>Batas: {{ \Carbon\Carbon::parse($kbt->batas_tanggal_butuh)->format('d M Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Penawaran Berjalan -->
            <div class="card-custom p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-tag text-warning me-2"></i> Pengajuan Penawaran Saya</h6>
                    <a href="{{ route('pembeli.penawaran.index') }}" class="small text-pp-primary text-decoration-none">Semua</a>
                </div>

                @if($penawaranBerjalan->isEmpty())
                    <p class="text-muted small mb-0">Belum ada pengajuan penawaran yang sedang berjalan.</p>
                @else
                    <div class="d-flex flex-column gap-2">
                        @foreach($penawaranBerjalan as $pnw)
                            <div class="p-2 bg-light rounded border small">
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-white text-dark border">{{ $pnw->kode_penawaran }}</span>
                                    <span class="badge {{ $pnw->status === 'disetujui' ? 'bg-success' : ($pnw->status === 'selesai' ? 'bg-primary' : 'bg-warning text-dark') }}">
                                        {{ ucfirst(str_replace('_', ' ', $pnw->status)) }}
                                    </span>
                                </div>
                                <div class="fw-bold text-dark mt-1">{{ $pnw->rencanaPanen->komoditas->nama }} &bull; {{ number_format($pnw->jumlah_kg, 0) }} kg</div>
                                <div class="text-muted d-flex justify-content-between" style="font-size: 0.75rem;">
                                    <span>Petani: {{ $pnw->petani->name }}</span>
                                    <span class="fw-bold text-success">Rp {{ number_format($pnw->total_harga, 0, ',', '.') }}</span>
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
        // Init Leaflet Map Dashboard Pembeli
        const buyerLat = {{ $user->latitude ?? -6.7120 }};
        const buyerLng = {{ $user->longitude ?? 108.5123 }};

        const map = L.map('petaPembeli').setView([buyerLat, buyerLng], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Marker Pembeli
        L.marker([buyerLat, buyerLng], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(map).bindPopup("<strong>Lokasi Usaha Anda:</strong><br>{{ $user->institution ?? $user->name }}").openPopup();

        // Markers Petani Surplus
        @foreach($surplusCocok as $item)
            @if($item->latitude && $item->longitude)
                L.marker([{{ $item->latitude }}, {{ $item->longitude }}], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map).bindPopup("<strong>{{ $item->komoditas->nama }}</strong><br>Surplus: {{ number_format($item->potensi_surplus_kg, 0) }} kg<br>Petani: {{ $item->petani->name }}<br><a href='{{ route('pembeli.jelajah-surplus.detail', $item->id) }}' class='btn btn-xs btn-success text-white mt-1 py-1 px-2 d-inline-block'>Lihat & Tawar</a>");
            @endif
        @endforeach
    });
</script>
@endpush
