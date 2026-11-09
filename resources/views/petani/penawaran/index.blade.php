@extends('layouts.app')

@section('title', 'Kelola Penawaran Pembeli - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('petani.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Penawaran</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Kelola Penawaran & Kesepakatan</h2>
            <small class="text-muted">Pantau pengajuan pembelian surplus dan konfirmasi mitra pembeli</small>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('petani.rencana-panen.index') }}" class="btn btn-pp-primary btn-sm">
                <i class="bi bi-calendar4-week me-1"></i> Rencana Panen & Cari Pembeli
            </a>
        </div>
    </div>

    <!-- Filter Status -->
    <div class="card-custom p-3 bg-white mb-4">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('petani.penawaran.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-pp-primary' : 'btn-light border' }}">
                Semua Penawaran
            </a>
            <a href="{{ route('petani.penawaran.index', ['status' => 'menunggu_konfirmasi']) }}" class="btn btn-sm {{ request('status') === 'menunggu_konfirmasi' ? 'btn-warning text-dark' : 'btn-light border' }}">
                Menunggu Persetujuan
            </a>
            <a href="{{ route('petani.penawaran.index', ['status' => 'disetujui']) }}" class="btn btn-sm {{ request('status') === 'disetujui' ? 'btn-success' : 'btn-light border' }}">
                Disetujui (Siap Kirim)
            </a>
            <a href="{{ route('petani.penawaran.index', ['status' => 'selesai']) }}" class="btn btn-sm {{ request('status') === 'selesai' ? 'btn-primary' : 'btn-light border' }}">
                Selesai Tersalurkan
            </a>
        </div>
    </div>

    <!-- Tabel Penawaran -->
    <div class="card-custom p-4 bg-white">
        @if($penawaranList->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                <h5 class="fw-bold text-dark">Belum Ada Penawaran</h5>
                <p class="small text-muted">Buka detail rencana panen Anda untuk melihat calon pembeli yang cocok dan ajukan penawaran.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Komoditas & Rencana</th>
                            <th>Mitra Pembeli</th>
                            <th>Inisiator</th>
                            <th>Kuantitas</th>
                            <th>Harga Satuan</th>
                            <th>Total Nilai</th>
                            <th>Rencana Ambil</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penawaranList as $pen)
                            @php
                                $statusBadge = match($pen->status) {
                                    'menunggu_konfirmasi' => 'bg-warning bg-opacity-25 text-warning text-dark border border-warning',
                                    'disetujui' => 'bg-success bg-opacity-25 text-success border border-success',
                                    'selesai' => 'bg-primary bg-opacity-25 text-primary border border-primary',
                                    'ditolak' => 'bg-danger bg-opacity-25 text-danger border border-danger',
                                    default => 'bg-light text-muted border'
                                };
                            @endphp
                            <tr>
                                <td><span class="badge bg-light text-dark border">{{ $pen->kode_penawaran }}</span></td>
                                <td>
                                    <strong class="d-block text-dark">{{ $pen->rencanaPanen->komoditas->nama }}</strong>
                                    <small class="text-muted">{{ $pen->rencanaPanen->kode_rencana }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $pen->pembeli->institution ?? $pen->pembeli->name }}</div>
                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i> {{ $pen->pembeli->phone ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $pen->inisiator_role === 'petani' ? 'bg-info bg-opacity-25 text-info text-dark' : 'bg-secondary bg-opacity-25 text-secondary' }}">
                                        {{ $pen->inisiator_role === 'petani' ? 'Diajukan Anda' : 'Dari Pembeli' }}
                                    </span>
                                </td>
                                <td><strong>{{ number_format($pen->jumlah_kg, 0) }} kg</strong></td>
                                <td>Rp {{ number_format($pen->harga_per_kg, 0, ',', '.') }}/kg</td>
                                <td class="fw-bold text-pp-primary">Rp {{ number_format($pen->total_harga, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($pen->tanggal_pengambilan)->isoFormat('D MMM Y') }}</td>
                                <td>
                                    <span class="badge {{ $statusBadge }} px-2 py-1">
                                        {{ ucfirst(str_replace('_', ' ', $pen->status)) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if($pen->status === 'menunggu_konfirmasi' && $pen->inisiator_role !== 'petani')
                                        <div class="btn-group btn-group-sm">
                                            <form action="{{ route('petani.penawaran.terima', $pen->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success py-1 px-2" title="Terima">
                                                    <i class="bi bi-check-lg"></i> Terima
                                                </button>
                                            </form>
                                            <form action="{{ route('petani.penawaran.tolak', $pen->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger py-1 px-2" title="Tolak">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($pen->status === 'disetujui')
                                        <a href="{{ route('petani.penyaluran.formCatat', $pen->id) }}" class="btn btn-sm btn-pp-primary py-1 px-2">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Catat Penyaluran
                                        </a>
                                    @elseif($pen->status === 'selesai')
                                        <span class="badge bg-light text-success border"><i class="bi bi-check-all"></i> Selesai</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $penawaranList->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
