@extends('layouts.app')

@section('title', 'Daftar Pengajuan Penawaran - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('pembeli.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Penawaran Saya</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Pengajuan Pembelian & Kontrak</h2>
            <small class="text-muted">Pantau status persetujuan petani terhadap penawaran surplus Anda</small>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('pembeli.jelajah-surplus') }}" class="btn btn-pp-primary btn-sm">
                <i class="bi bi-search me-1"></i> Jelajah Surplus Lagi
            </a>
        </div>
    </div>

    <div class="card-custom p-4 bg-white">
        @if($penawaranList->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                <h5 class="fw-bold text-dark">Belum Ada Pengajuan Penawaran</h5>
                <p class="small text-muted mb-3">Cari hasil panen di menu Jelajah Surplus dan ajukan pembelian langsung ke petani.</p>
                <a href="{{ route('pembeli.jelajah-surplus') }}" class="btn btn-pp-primary btn-sm">Jelajah Sekarang</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Komoditas</th>
                            <th>Petani Mitra</th>
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
                                    'dibatalkan' => 'bg-light text-muted border',
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
                                    <strong class="d-block">{{ $pen->petani->name }}</strong>
                                    <small class="text-muted">{{ $pen->petani->address }}</small>
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
                                    @if($pen->status === 'menunggu_konfirmasi')
                                        <form action="{{ route('pembeli.penawaran.batalkan', $pen->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-xs py-1 px-2">
                                                Batalkan
                                            </button>
                                        </form>
                                    @elseif($pen->status === 'disetujui')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 py-1 px-2">
                                            <i class="bi bi-clock-history me-1"></i> Menunggu Panen & Kirim
                                        </span>
                                    @elseif($pen->status === 'selesai')
                                        <a href="{{ route('pembeli.penyaluran.index') }}" class="btn btn-outline-primary btn-xs py-1 px-2">
                                            Lihat Penerimaan
                                        </a>
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
                {{ $penawaranList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
