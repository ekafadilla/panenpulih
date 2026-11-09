@extends('layouts.app')

@section('title', 'Manajemen Pengguna - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Pengguna</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Manajemen Pengguna & Verifikasi Mitra</h2>
            <small class="text-muted">Kelola akun petani, pedagang, dan UMKM yang berpartisipasi dalam platform</small>
        </div>
    </div>

    <!-- Filter Role -->
    <div class="card-custom p-3 bg-white mb-4">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm {{ !request('role') ? 'btn-pp-primary' : 'btn-light border' }}">
                Semua Pengguna
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'petani']) }}" class="btn btn-sm {{ request('role') === 'petani' ? 'btn-success' : 'btn-light border' }}">
                <i class="bi bi-flower1 me-1"></i> Petani
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'pedagang']) }}" class="btn btn-sm {{ request('role') === 'pedagang' ? 'btn-primary' : 'btn-light border' }}">
                <i class="bi bi-truck me-1"></i> Pedagang
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'umkm']) }}" class="btn btn-sm {{ request('role') === 'umkm' ? 'btn-warning text-dark' : 'btn-light border' }}">
                <i class="bi bi-shop me-1"></i> UMKM Pengolah
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="btn btn-sm {{ request('role') === 'admin' ? 'btn-dark' : 'btn-light border' }}">
                Admin
            </a>
        </div>
    </div>

    <!-- Tabel Pengguna -->
    <div class="card-custom p-4 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Email & Kontak</th>
                        <th>Peran (Role)</th>
                        <th>Institusi / Usaha</th>
                        <th>Alamat Domisili</th>
                        <th>Status Verifikasi</th>
                        <th class="text-end">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        @php
                            $roleBadge = match($u->role) {
                                'petani' => 'bg-success bg-opacity-25 text-success border border-success',
                                'pedagang' => 'bg-primary bg-opacity-25 text-primary border border-primary',
                                'umkm' => 'bg-warning bg-opacity-25 text-warning text-dark border border-warning',
                                'admin' => 'bg-dark text-white',
                                default => 'bg-light text-muted'
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong class="d-block text-dark">{{ $u->name }}</strong>
                                <small class="text-muted">Terdaftar: {{ $u->created_at->format('d M Y') }}</small>
                            </td>
                            <td>
                                <div>{{ $u->email }}</div>
                                <small class="text-muted">{{ $u->phone ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $roleBadge }} text-uppercase">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $u->institution ?? '-' }}</span>
                            </td>
                            <td class="text-muted">{{ Str::limit($u->address, 30) }}</td>
                            <td>
                                @if($u->is_verified)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                        <i class="bi bi-patch-check-fill me-1"></i> Terverifikasi
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                        Belum Diverifikasi
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form action="{{ route('admin.users.verifikasi', $u->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-xs {{ $u->is_verified ? 'btn-outline-danger' : 'btn-outline-success' }} py-1 px-2">
                                        {{ $u->is_verified ? 'Non-aktifkan' : 'Verifikasi Akun' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
