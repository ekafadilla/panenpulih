@extends('layouts.app')

@section('title', 'Master Komoditas Pertanian - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Master Komoditas</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Master Komoditas Pertanian</h2>
            <small class="text-muted">Konfigurasi indeks kerentanan pangan (U), daya simpan, dan harga acuan pasar</small>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('admin.komoditas.create') }}" class="btn btn-pp-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> Tambah Komoditas Baru
            </a>
        </div>
    </div>

    <div class="card-custom p-4 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Komoditas</th>
                        <th>Kategori</th>
                        <th>Umur Simpan</th>
                        <th>Skor Kerentanan (U)</th>
                        <th>Harga Acuan / kg</th>
                        <th>Rencana Panen Aktif</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($komoditasList as $k)
                        @php
                            $uBadge = match(true) {
                                $k->skor_kerentanan >= 75 => 'badge-risk-sangat-tinggi',
                                $k->skor_kerentanan >= 50 => 'badge-risk-tinggi',
                                $k->skor_kerentanan >= 30 => 'badge-risk-sedang',
                                default => 'badge-risk-rendah'
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong class="d-block text-dark">{{ $k->nama }}</strong>
                                <small class="text-muted fst-italic">{{ $k->nama_latin ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-uppercase">{{ $k->kategori }}</span>
                            </td>
                            <td>~{{ $k->umur_simpan_hari }} hari</td>
                            <td>
                                <span class="badge {{ $uBadge }} px-2 py-1">
                                    {{ $k->skor_kerentanan }} / 100
                                </span>
                            </td>
                            <td class="fw-bold text-dark">Rp {{ number_format($k->harga_acuan_per_kg, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-pp-primary bg-opacity-10 text-pp-primary border border-pp-primary border-opacity-25">
                                    {{ $k->rencana_panen_count }} rencana
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.komoditas.edit', $k->id) }}" class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($k->rencana_panen_count == 0)
                                        <form action="{{ route('admin.komoditas.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus komoditas ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $komoditasList->links() }}
        </div>
    </div>
</div>
@endsection
