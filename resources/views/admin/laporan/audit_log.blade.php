@extends('layouts.app')

@section('title', 'Audit Trail Sistem - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Audit Trail</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Log Riwayat Status & Audit Trail</h2>
            <small class="text-muted">Pencatatan transparan seluruh perubahan status rencana panen, penawaran, dan penyaluran</small>
        </div>
    </div>

    <div class="card-custom p-4 bg-white">
        @if($logs->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                <h5 class="fw-bold text-dark">Belum Ada Catatan Riwayat</h5>
                <p class="small text-muted mb-0">Aktivitas status akan otomatis tercatat saat proses bisnis berjalan.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu Aktivitas</th>
                            <th>Entitas Proses</th>
                            <th>ID Referensi</th>
                            <th>Status Sebelum</th>
                            <th>Status Baru</th>
                            <th>Pengguna Terkait</th>
                            <th>Keterangan / Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>
                                    <span class="d-block text-dark fw-semibold">{{ $log->created_at->isoFormat('D MMM Y, HH:mm') }}</span>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border text-uppercase">
                                        {{ str_replace('_', ' ', $log->referensi_tipe) }}
                                    </span>
                                </td>
                                <td><code>#{{ $log->referensi_id }}</code></td>
                                <td>
                                    @if($log->status_lama)
                                        <span class="badge bg-light text-muted border">{{ $log->status_lama }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-pp-primary bg-opacity-10 text-pp-primary border border-pp-primary border-opacity-25 fw-bold">
                                        {{ $log->status_baru }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="d-block text-dark">{{ $log->user->name ?? 'Sistem / Otomatis' }}</strong>
                                    <small class="text-muted">{{ ucfirst($log->user->role ?? 'System') }}</small>
                                </td>
                                <td class="text-secondary">{{ $log->catatan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
