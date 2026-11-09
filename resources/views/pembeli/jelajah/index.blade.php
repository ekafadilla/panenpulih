@extends('layouts.app')

@section('title', 'Jelajah Surplus Panen Pra-Panen - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('pembeli.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Jelajah Surplus</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Eksplorasi Surplus Pra-Panen</h2>
            <small class="text-muted">Temukan hasil panen berisiko surplus sebelum panen tiba dan dapatkan pasokan bahan baku segar dengan harga terbaik</small>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('pembeli.kebutuhan.create') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Daftarkan Kebutuhan Saya
            </a>
        </div>
    </div>

    <!-- Filter Multi-Kriteria Bar -->
    <div class="card-custom p-4 bg-white mb-4">
        <form method="GET" action="{{ route('pembeli.jelajah-surplus') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Komoditas</label>
                <select name="komoditas_id" class="form-select form-select-sm">
                    <option value="">-- Semua Komoditas --</option>
                    @foreach($komoditasList as $k)
                        <option value="{{ $k->id }}" {{ request('komoditas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold">Tingkat Risiko Surplus</label>
                <select name="kategori_risiko" class="form-select form-select-sm">
                    <option value="">-- Semua Risiko --</option>
                    <option value="Sangat Tinggi" {{ request('kategori_risiko') == 'Sangat Tinggi' ? 'selected' : '' }}>Sangat Tinggi (Paling Butuh Penyerapan)</option>
                    <option value="Tinggi" {{ request('kategori_risiko') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                    <option value="Sedang" {{ request('kategori_risiko') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="Rendah" {{ request('kategori_risiko') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold">Toleransi Grade</label>
                <select name="grade" class="form-select form-select-sm">
                    <option value="">-- Semua Grade --</option>
                    <option value="grade_a" {{ request('grade') == 'grade_a' ? 'selected' : '' }}>Grade A</option>
                    <option value="grade_b" {{ request('grade') == 'grade_b' ? 'selected' : '' }}>Grade B</option>
                    <option value="campur" {{ request('grade') == 'campur' ? 'selected' : '' }}>Campuran</option>
                    <option value="imperfect" {{ request('grade') == 'imperfect' ? 'selected' : '' }}>Imperfect</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold">Urgensi Waktu</label>
                <select name="urgensi" class="form-select form-select-sm">
                    <option value="">-- Kapan Saja --</option>
                    <option value="3" {{ request('urgensi') == '3' ? 'selected' : '' }}>&le; 3 Hari (Mendesak)</option>
                    <option value="7" {{ request('urgensi') == '7' ? 'selected' : '' }}>&le; 7 Hari (1 Minggu)</option>
                    <option value="14" {{ request('urgensi') == '14' ? 'selected' : '' }}>&le; 14 Hari (2 Minggu)</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-pp-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('pembeli.jelajah-surplus') }}" class="btn btn-sm btn-light border text-muted">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Peta Sebaran Surplus -->
    <div class="card-custom p-4 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-geo-alt-fill text-danger me-2"></i> Peta Sebaran Rencana Panen Petani</h5>
            <small class="text-muted">{{ count($mapMarkers) }} lokasi panen terdeteksi</small>
        </div>
        <div id="mapJelajah" class="map-container mb-2"></div>
    </div>

    <!-- Grid Kartu Surplus -->
    <div class="row g-4">
        @forelse($surplusList as $item)
            @php
                $hariLagi = max(0, (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($item->tanggal_perkiraan_panen), false));
                $risiko = $item->analisisSurplus?->kategori_risiko ?? 'Sedang';
                $badgeClass = match($risiko) {
                    'Rendah' => 'badge-risk-rendah',
                    'Sedang' => 'badge-risk-sedang',
                    'Tinggi' => 'badge-risk-tinggi',
                    'Sangat Tinggi' => 'badge-risk-sangat-tinggi',
                    default => 'bg-secondary text-white'
                };
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card-custom h-100 p-4 bg-white d-flex flex-column justify-content-between shadow-sm">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-light text-dark border">{{ $item->kode_rencana }}</span>
                            <span class="badge {{ $badgeClass }} px-2 py-1">Risiko: {{ $risiko }}</span>
                        </div>

                        <h5 class="fw-bold text-dark mb-1">{{ $item->komoditas->nama }}</h5>
                        <div class="small text-muted mb-2">
                            <i class="bi bi-flower1 text-success me-1"></i> {{ $item->petani->name }}
                        </div>
                        <div class="small text-muted mb-3">
                            <i class="bi bi-geo-alt text-danger me-1"></i> {{ $item->lokasi }}
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-3 small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Estimasi Panen (H):</span>
                                <strong>{{ number_format($item->estimasi_hasil_kg, 0) }} kg</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Potensi Surplus (S):</span>
                                <strong class="text-pp-primary fs-6">{{ number_format($item->potensi_surplus_kg, 0) }} kg</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Grade Produk:</span>
                                <span class="badge bg-white border text-dark text-uppercase">{{ $item->grade }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-1 text-dark">
                                <span>Ekspektasi Petani:</span>
                                <strong class="text-success">
                                    {{ $item->ekspektasi_harga_per_kg ? 'Rp ' . number_format($item->ekspektasi_harga_per_kg, 0, ',', '.') . '/kg' : 'Negosiasi Terbuka' }}
                                </strong>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
                            <span><i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($item->tanggal_perkiraan_panen)->isoFormat('D MMM Y') }}</span>
                            <span class="badge bg-light text-dark border">{{ $hariLagi }} hari lagi</span>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('pembeli.jelajah-surplus.detail', $item->id) }}" class="btn btn-sm btn-pp-primary fw-semibold">
                                <i class="bi bi-cart-plus me-1"></i> Detail & Ajukan Penawaran
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-search fs-1 d-block mb-2 text-muted"></i>
                <h5 class="fw-bold text-dark">Tidak Ada Surplus yang Sesuai Filter</h5>
                <p class="small text-muted mb-0">Coba atur ulang kriteria pencarian atau komoditas lain.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $surplusList->withQueryString()->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const markersData = @json($mapMarkers);

        const map = L.map('mapJelajah').setView([-6.5, 107.8], 8);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        if (markersData.length > 0) {
            const bounds = [];
            markersData.forEach(item => {
                if (item.lat && item.lng) {
                    const marker = L.marker([item.lat, item.lng]).addTo(map);
                    marker.bindPopup(`
                        <strong>${item.komoditas} (${item.surplus_kg} kg)</strong><br>
                        Petani: ${item.petani}<br>
                        Panen: ${item.tgl_panen} (${item.hari_lagi} hari lagi)<br>
                        Tingkat Risiko: <strong>${item.risiko}</strong><br>
                        Harga: ${item.harga}<br>
                        <a href="/pembeli/jelajah-surplus/${item.id}" class="btn btn-xs btn-success text-white py-1 px-2 mt-1 d-inline-block">Detail Surplus</a>
                    `);
                    bounds.push([item.lat, item.lng]);
                }
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }
    });
</script>
@endpush
