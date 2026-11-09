@extends('layouts.app')

@section('title', 'Riwayat Penyaluran & Dampak Surplus - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('petani.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Penyaluran</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Realisasi Penyaluran Surplus</h2>
            <small class="text-muted">Pantau penyerapan hasil panen yang berhasil disalurkan dan mencegah food loss</small>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('petani.penawaran.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-tag me-1"></i> Lihat Penawaran
            </a>
        </div>
    </div>

    <!-- Tabel Penyaluran -->
    <div class="card-custom p-4 bg-white">
        @if($penyaluranList->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                <h5 class="fw-bold text-dark">Belum Ada Catatan Penyaluran</h5>
                <p class="small text-muted mb-0">Setelah kesepakatan penawaran disetujui, Anda dapat mencatat jumlah hasil panen yang berhasil diserahkan kepada pembeli.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Penyaluran</th>
                            <th>Tanggal Kirim</th>
                            <th>Komoditas</th>
                            <th>Mitra Penerima</th>
                            <th>Realisasi (kg)</th>
                            <th>Sisa Surplus (kg)</th>
                            <th>Tingkat Penyerapan</th>
                            <th>Nilai Transaksi</th>
                            <th>Rating Pembeli</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penyaluranList as $item)
                            <tr>
                                <td><span class="badge bg-light text-dark border">{{ $item->kode_penyaluran }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_penyaluran)->isoFormat('D MMM Y') }}</td>
                                <td>
                                    <strong class="d-block">{{ $item->penawaran->rencanaPanen->komoditas->nama }}</strong>
                                    <small class="text-muted">{{ $item->penawaran->rencanaPanen->kode_rencana }}</small>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $item->penawaran->pembeli->institution ?? $item->penawaran->pembeli->name }}</span>
                                    <small class="d-block text-muted">{{ ucfirst($item->penawaran->pembeli->role) }}</small>
                                </td>
                                <td><strong class="text-pp-primary fs-6">{{ number_format($item->jumlah_realisasi_kg, 0) }} kg</strong></td>
                                <td class="text-muted">{{ number_format($item->sisa_surplus_kg, 0) }} kg</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                            <div class="progress-bar bg-success" style="width: {{ min(100, $item->tingkat_penyerapan_persen) }}%;"></div>
                                        </div>
                                        <span class="badge bg-success bg-opacity-25 text-success fw-bold">{{ $item->tingkat_penyerapan_persen }}%</span>
                                    </div>
                                </td>
                                <td class="fw-bold text-dark">Rp {{ number_format($item->harga_total_realisasi, 0, ',', '.') }}</td>
                                <td>
                                    @if($item->rating_pembeli)
                                        <div class="text-warning">
                                            @for($i=1; $i<=$item->rating_pembeli; $i++)
                                                <i class="bi bi-star-fill"></i>
                                            @endfor
                                        </div>
                                        <small class="text-muted d-block">{{ Str::limit($item->ulasan_pembeli, 25) }}</small>
                                    @else
                                        <span class="text-muted small">Menunggu ulasan</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $penyaluranList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
