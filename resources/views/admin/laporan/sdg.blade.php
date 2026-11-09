@extends('layouts.app')

@section('title', 'Laporan Dampak SDG 12 & SDG 2 - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Laporan SDG 12 & 2</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h2 class="fw-bold text-dark mb-0">Laporan Capaian Dampak SDG 12 & SDG 2</h2>
                <span class="badge bg-pp-primary px-3 py-1">Tahun 2026</span>
            </div>
            <small class="text-muted">Evaluasi penyerapan surplus pertanian, reduksi emisi, dan nilai ekonomi pangan terselamatkan</small>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-printer"></i> Cetak Dokumen / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Ringkasan Eksekutif Dampak -->
    <div class="card-custom p-4 bg-white mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-globe-americas text-pp-primary me-2"></i> Ringkasan Eksekutif Dampak Keberlanjutan</h5>
        <p class="text-muted small mb-4" style="line-height: 1.7;">
            Berdasarkan kajian historis Bappenas (2000–2019), timbulan food loss and waste di Indonesia diperkirakan mencapai 23–48 juta ton/tahun atau sekitar 115–184 kg per kapita/tahun dengan kerugian ekonomi Rp213–551 triliun/tahun. PANENPULIH beroperasi sebagai intervensi hulu (pre-harvest intelligence) guna mencegah kehilangan hasil panen sebelum masa petik tiba, menyalurkan kelebihan hasil pertanian ke rantai pasok pedagang dan UMKM olahan pangan.
        </p>

        <div class="row g-3 text-center">
            <div class="col-md-3">
                <div class="p-3 rounded-4 bg-emerald-50 border border-emerald-200">
                    <div class="text-emerald-800 small fw-bold mb-1"><i class="bi bi-recycle me-1"></i> SDG 12.3: Food Loss Dicegah</div>
                    <h2 class="fw-extrabold text-pp-primary mb-0">{{ $metrik['food_loss_dicegah_ton'] }} Ton</h2>
                    <small class="text-muted">({{ number_format($metrik['total_tersalurkan_kg'], 0, ',', '.') }} kg pangan terselamatkan)</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 rounded-4 bg-light border">
                    <div class="text-muted small fw-bold mb-1"><i class="bi bi-cash-stack me-1"></i> Nilai Ekonomi Terselamatkan</div>
                    <h2 class="fw-extrabold text-dark mb-0">Rp {{ number_format($metrik['total_nilai_ekonomi_rp'] / 1000000, 1, ',', '.') }} Jt</h2>
                    <small class="text-muted">Realisasi transaksi tersalurkan</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 rounded-4 bg-amber-50 border border-amber-200" style="background-color: #fffbeb;">
                    <div class="text-amber-800 small fw-bold mb-1" style="color: #92400e;"><i class="bi bi-cup-hot me-1"></i> SDG 2: Nol Kelaparan</div>
                    <h2 class="fw-extrabold text-dark mb-0">{{ number_format($metrik['estimasi_porsi_pangan'], 0, ',', '.') }}</h2>
                    <small class="text-muted">Porsi makan terselamatkan</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="p-3 rounded-4 bg-sky-50 border border-sky-200" style="background-color: #f0f9ff;">
                    <div class="text-sky-800 small fw-bold mb-1" style="color: #075985;"><i class="bi bi-cloud-slash me-1"></i> Reduksi Jejak Karbon</div>
                    <h2 class="fw-extrabold text-dark mb-0">{{ number_format($metrik['estimasi_co2e_dicegah_kg'], 0, ',', '.') }}</h2>
                    <small class="text-muted">kg CO₂e emisi terhindar</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Rincian Realisasi Penyaluran -->
    <div class="card-custom p-4 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clipboard-check text-success me-2"></i> Log Transaksi & Realisasi Penyerapan Surplus</h5>
            <span class="badge bg-light text-dark border">{{ $semuaPenyaluran->count() }} Penyaluran Terverifikasi</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode Penyaluran</th>
                        <th>Tanggal</th>
                        <th>Komoditas</th>
                        <th>Petani & Lokasi</th>
                        <th>Penerima (Pedagang/UMKM)</th>
                        <th>Realisasi (kg)</th>
                        <th>Tingkat Penyerapan</th>
                        <th>Nilai Transaksi (Rp)</th>
                        <th>Dampak CO₂e</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($semuaPenyaluran as $item)
                        <tr>
                            <td><span class="badge bg-light text-dark border">{{ $item->kode_penyaluran }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_penyaluran)->isoFormat('D MMM Y') }}</td>
                            <td><strong class="text-dark">{{ $item->penawaran->rencanaPanen->komoditas->nama }}</strong></td>
                            <td>
                                <div>{{ $item->penawaran->petani->name }}</div>
                                <small class="text-muted">{{ $item->penawaran->petani->address }}</small>
                            </td>
                            <td>
                                <div>{{ $item->penawaran->pembeli->institution ?? $item->penawaran->pembeli->name }}</div>
                                <span class="badge {{ $item->penawaran->pembeli->role === 'umkm' ? 'bg-warning text-dark' : 'bg-primary' }}" style="font-size: 0.65rem;">
                                    {{ strtoupper($item->penawaran->pembeli->role) }}
                                </span>
                            </td>
                            <td><strong class="text-pp-primary fs-6">{{ number_format($item->jumlah_realisasi_kg, 0) }} kg</strong></td>
                            <td>
                                <span class="badge bg-success bg-opacity-25 text-success fw-bold">
                                    {{ $item->tingkat_penyerapan_persen }}%
                                </span>
                            </td>
                            <td class="fw-bold text-dark">Rp {{ number_format($item->harga_total_realisasi, 0, ',', '.') }}</td>
                            <td class="text-muted">{{ number_format($item->jumlah_realisasi_kg * 1.9, 1) }} kg CO₂e</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Belum ada realisasi penyaluran terverifikasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Metodologi Perhitungan Dampak -->
    <div class="card-custom p-4 bg-light">
        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle me-1"></i> Catatan Metodologi & Parameter Perhitungan</h6>
        <div class="row g-3 small text-muted">
            <div class="col-md-6">
                <strong>1. Rumus Tingkat Penyerapan (Section 11):</strong>
                <p class="mb-2"><code>Tingkat Penyerapan = (Jumlah Berhasil Disalurkan ÷ Potensi Surplus Aktual) × 100%</code>. Dihitung per transaksi dan diagregasi untuk platform.</p>
            </div>
            <div class="col-md-6">
                <strong>2. Faktor Emisi Karbon (FAO & IPCC):</strong>
                <p class="mb-2">Mengacu pada faktor rata-rata jejak karbon limbah komoditas hortikultura segar sebesar ~1.9 kg CO₂e per 1 kg pangan yang diselamatkan dari pembusukan.</p>
            </div>
        </div>
    </div>
</div>
@endsection
