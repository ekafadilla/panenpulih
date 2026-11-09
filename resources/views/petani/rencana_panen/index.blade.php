@extends('layouts.app')

@section('title', 'Daftar Rencana Panen - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('petani.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Rencana Panen</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Rencana Panen & Potensi Surplus</h2>
            <small class="text-muted">Kelola estimasi hasil pertanian sebelum masa panen tiba</small>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('petani.rencana-panen.create') }}" class="btn btn-pp-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> Input Rencana Panen Baru
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card-custom p-3 mb-4 bg-white">
        <form method="GET" action="{{ route('petani.rencana-panen.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <select name="komoditas_id" class="form-select form-select-sm">
                    <option value="">-- Semua Komoditas --</option>
                    @foreach($komoditasList as $k)
                        <option value="{{ $k->id }}" {{ request('komoditas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }} ({{ ucfirst($k->kategori) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif (Pra-Panen)</option>
                    <option value="panen" {{ request('status') == 'panen' ? 'selected' : '' }}>Sedang Panen</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai Disalurkan</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('petani.rencana-panen.index') }}" class="btn btn-sm btn-light border text-muted">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Rencana Panen -->
    <div class="card-custom p-4 bg-white">
        @if($rencanaList->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                <h5 class="fw-bold text-dark">Belum Ada Rencana Panen</h5>
                <p class="small text-muted mb-3">Mulai dengan mencatat perkiraan panen pertama Anda untuk mengaktifkan sistem deteksi surplus.</p>
                <a href="{{ route('petani.rencana-panen.create') }}" class="btn btn-pp-primary btn-sm">Input Rencana Panen</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Komoditas</th>
                            <th>Estimasi (H)</th>
                            <th>Terpesan (P)</th>
                            <th>Surplus (S)</th>
                            <th>Tgl Panen</th>
                            <th>Risiko Pre-Harvest</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rencanaList as $r)
                            @php
                                $hariLagi = max(0, (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($r->tanggal_perkiraan_panen), false));
                                $risiko = $r->analisisSurplus?->kategori_risiko ?? 'Sedang';
                                $badgeClass = match($risiko) {
                                    'Rendah' => 'badge-risk-rendah',
                                    'Sedang' => 'badge-risk-sedang',
                                    'Tinggi' => 'badge-risk-tinggi',
                                    'Sangat Tinggi' => 'badge-risk-sangat-tinggi',
                                    default => 'bg-secondary text-white'
                                };
                            @endphp
                            <tr>
                                <td><span class="badge bg-light text-dark border">{{ $r->kode_rencana }}</span></td>
                                <td>
                                    <strong class="d-block text-dark">{{ $r->komoditas->nama }}</strong>
                                    <small class="text-muted">{{ $r->lokasi }}</small>
                                </td>
                                <td><strong>{{ number_format($r->estimasi_hasil_kg, 0) }}</strong> kg</td>
                                <td class="text-muted">{{ number_format($r->jumlah_terpesan_kg, 0) }} kg</td>
                                <td>
                                    <span class="fw-bold text-pp-primary fs-6">{{ number_format($r->potensi_surplus_kg, 0) }} kg</span>
                                    <small class="d-block text-muted">
                                        ({{ $r->estimasi_hasil_kg > 0 ? round(($r->potensi_surplus_kg / $r->estimasi_hasil_kg) * 100, 1) : 0 }}%)
                                    </small>
                                </td>
                                <td>
                                    <span class="d-block fw-semibold text-dark">{{ \Carbon\Carbon::parse($r->tanggal_perkiraan_panen)->isoFormat('D MMM Y') }}</span>
                                    <small class="badge bg-light text-dark border">{{ $hariLagi }} hari lagi</small>
                                </td>
                                <td>
                                    <span class="badge {{ $badgeClass }} px-2 py-1">
                                        {{ $risiko }} ({{ $r->analisisSurplus?->skor_risiko_total ?? 0 }})
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $r->status === 'aktif' ? 'bg-success bg-opacity-25 text-success' : 'bg-light text-muted border' }}">
                                        {{ ucfirst($r->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('petani.rencana-panen.show', $r->id) }}" class="btn btn-outline-success" title="Detail & Rekomendasi Pembeli">
                                            <i class="bi bi-people me-1"></i> Cocokkan
                                        </a>
                                        <a href="{{ route('petani.rencana-panen.edit', $r->id) }}" class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $rencanaList->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
