@extends('layouts.app')

@section('title', 'Masuk ke Platform - PANENPULIH')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card-custom p-4 p-md-5 border-0 shadow-lg">
                <div class="text-center mb-4">
                    <div class="bg-pp-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 54px; height: 54px;">
                        <i class="bi bi-seedling fs-3"></i>
                    </div>
                    <h3 class="fw-bold">Masuk PANENPULIH</h3>
                    <p class="text-muted small">Platform Deteksi Surplus Panen & Distribusi Cerdas</p>
                </div>

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label small fw-semibold">Kata Sandi</label>
                            <span class="small text-muted">Default: <code>password</code></span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small" for="remember">Ingat Saya</label>
                    </div>

                    <button type="submit" class="btn btn-pp-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                    </button>
                </form>

                <div class="mt-4 pt-3 border-top text-center">
                    <p class="small text-muted mb-2">Atau gunakan akses cepat akun demo untuk pengujian:</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('auth.demo', 'petani') }}" class="btn btn-sm btn-outline-success text-start d-flex align-items-center justify-content-between px-3">
                            <span><i class="bi bi-flower1 me-2"></i> Pak Supardi (Petani Cabai & Tomat)</span>
                            <span class="badge bg-success bg-opacity-25 text-success">PETANI</span>
                        </a>
                        <a href="{{ route('auth.demo', 'pedagang') }}" class="btn btn-sm btn-outline-primary text-start d-flex align-items-center justify-content-between px-3">
                            <span><i class="bi bi-truck me-2"></i> H. Hendra (Distributor Pantura)</span>
                            <span class="badge bg-primary bg-opacity-25 text-primary">PEDAGANG</span>
                        </a>
                        <a href="{{ route('auth.demo', 'umkm') }}" class="btn btn-sm btn-outline-warning text-dark text-start d-flex align-items-center justify-content-between px-3">
                            <span><i class="bi bi-shop me-2"></i> Bu Broto (UMKM Sambal Botol)</span>
                            <span class="badge bg-warning bg-opacity-25 text-warning text-dark">UMKM</span>
                        </a>
                        <a href="{{ route('auth.demo', 'admin') }}" class="btn btn-sm btn-outline-dark text-start d-flex align-items-center justify-content-between px-3">
                            <span><i class="bi bi-shield-check me-2"></i> Dr. Rahmat (Bappenas / Tim Pangan)</span>
                            <span class="badge bg-dark bg-opacity-25 text-dark">ADMIN</span>
                        </a>
                    </div>
                </div>

                <div class="text-center mt-4 small text-muted">
                    Belum punya akun? <a href="{{ route('register') }}" class="text-pp-primary fw-bold text-decoration-none">Daftar di sini</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
