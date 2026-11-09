@extends('layouts.app')

@section('title', 'Pendaftaran Akun - PANENPULIH')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card-custom p-4 p-md-5 border-0 shadow-lg">
                <div class="text-center mb-4">
                    <div class="bg-pp-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 54px; height: 54px;">
                        <i class="bi bi-person-plus-fill fs-3"></i>
                    </div>
                    <h3 class="fw-bold">Pendaftaran Akun Baru</h3>
                    <p class="text-muted small">Bergabung bersama jejaring penyelamatan surplus hasil panen Indonesia</p>
                </div>

                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    <!-- Pilih Role Pengguna -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Pilih Peran Akun Anda <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="border rounded-3 p-3 text-center d-block cursor-pointer h-100 role-card" for="role_petani" style="cursor: pointer;">
                                    <input type="radio" name="role" id="role_petani" value="petani" class="form-check-input mb-2" {{ old('role', 'petani') === 'petani' ? 'checked' : '' }}>
                                    <div class="fw-bold text-success"><i class="bi bi-flower1 fs-4 d-block mb-1"></i> Petani</div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Pemilik lahan & pengelola panen</small>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="border rounded-3 p-3 text-center d-block cursor-pointer h-100 role-card" for="role_pedagang" style="cursor: pointer;">
                                    <input type="radio" name="role" id="role_pedagang" value="pedagang" class="form-check-input mb-2" {{ old('role') === 'pedagang' ? 'checked' : '' }}>
                                    <div class="fw-bold text-primary"><i class="bi bi-truck fs-4 d-block mb-1"></i> Pedagang</div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Grosir, distributor & pengecer</small>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="border rounded-3 p-3 text-center d-block cursor-pointer h-100 role-card" for="role_umkm" style="cursor: pointer;">
                                    <input type="radio" name="role" id="role_umkm" value="umkm" class="form-check-input mb-2" {{ old('role') === 'umkm' ? 'checked' : '' }}>
                                    <div class="fw-bold text-warning text-dark"><i class="bi bi-shop fs-4 d-block mb-1"></i> UMKM Pengolah</div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Industri sambal, keripik, pasta pangan</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nama Lengkap Penanggung Jawab <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nama Kelompok Tani / Usaha / Toko</label>
                            <input type="text" name="institution" class="form-control" value="{{ old('institution') }}" placeholder="Contoh: Poktan Makmur / CV Rasa Enak">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email Pengguna <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="budi@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nomor WhatsApp / Kontak <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required placeholder="08123456789">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Alamat Lengkap (Desa, Kecamatan, Kabupaten) <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="2" required placeholder="Contoh: Desa Cantigi Kulon, Kec. Cantigi, Kab. Indramayu, Jawa Barat">{{ old('address') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Kata Sandi <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Ulangi Kata Sandi <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="Konfirmasi kata sandi">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-pp-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-check-circle me-1"></i> Daftarkan Akun Saya
                    </button>
                </form>

                <div class="text-center mt-4 small text-muted">
                    Sudah memiliki akun? <a href="{{ route('login') }}" class="text-pp-primary fw-bold text-decoration-none">Masuk di sini</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
