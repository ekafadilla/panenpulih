@extends('layouts.app')

@section('title', 'Kebutuhan Bahan Baku Saya - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('pembeli.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kebutuhan Saya</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Daftar Kebutuhan Komoditas</h2>
            <small class="text-muted">Kelola permintaan pasokan komoditas untuk dicocokkan dengan surplus petani pra-panen</small>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('pembeli.kebutuhan.create') }}" class="btn btn-pp-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> Tambah Kebutuhan Baru
            </a>
        </div>
    </div>

    <div class="card-custom p-4 bg-white">
        @if($kebutuhanList->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-basket fs-1 d-block mb-2"></i>
                <h5 class="fw-bold text-dark">Belum Ada Kebutuhan Terdaftar</h5>
                <p class="small text-muted mb-3">Daftarkan komoditas dan kuantitas yang dibutuhkan usaha Anda agar petani dapat menemukan Anda.</p>
                <a href="{{ route('pembeli.kebutuhan.create') }}" class="btn btn-pp-primary btn-sm">Input Kebutuhan Pertama</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Komoditas</th>
                            <th>Kuantitas Dibutuhkan</th>
                            <th>Rentang Waktu Dibutuhkan</th>
                            <th>Grade Diterima</th>
                            <th>Budget / kg</th>
                            <th>Tujuan Penggunaan</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kebutuhanList as $item)
                            <tr>
                                <td><span class="badge bg-light text-dark border">{{ $item->kode_kebutuhan }}</span></td>
                                <td>
                                    <strong class="d-block text-dark">{{ $item->komoditas->nama }}</strong>
                                    <small class="text-muted">{{ $item->lokasi }}</small>
                                </td>
                                <td><strong class="text-pp-primary fs-6">{{ number_format($item->jumlah_kg, 0) }} kg</strong></td>
                                <td>
                                    <span class="d-block text-dark">{{ \Carbon\Carbon::parse($item->tanggal_mulai_butuh)->format('d M') }} s/d {{ \Carbon\Carbon::parse($item->batas_tanggal_butuh)->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $item->grade_diterima === 'imperfect' ? 'bg-warning text-dark' : 'bg-light text-dark border' }}">
                                        {{ ucfirst($item->grade_diterima) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $item->budget_per_kg ? 'Rp ' . number_format($item->budget_per_kg, 0, ',', '.') : 'Fleksibel' }}
                                </td>
                                <td>{{ ucfirst(str_replace('_', ' ', $item->tujuan_penggunaan)) }}</td>
                                <td>
                                    <span class="badge {{ $item->status === 'aktif' ? 'bg-success bg-opacity-25 text-success' : 'bg-light text-muted border' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('pembeli.jelajah-surplus', ['komoditas_id' => $item->komoditas_id]) }}" class="btn btn-outline-success" title="Cari Surplus">
                                            <i class="bi bi-search me-1"></i> Cari
                                        </a>
                                        <a href="{{ route('pembeli.kebutuhan.edit', $item->id) }}" class="btn btn-outline-secondary" title="Edit">
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
                {{ $kebutuhanList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
