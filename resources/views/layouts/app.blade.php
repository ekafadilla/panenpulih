<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PANENPULIH - Smart Pre-Harvest Surplus Intelligence & Distribution Platform')</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        :root {
            --pp-primary: #059669;
            --pp-primary-dark: #047857;
            --pp-primary-light: #d1fae5;
            --pp-accent: #f59e0b;
            --pp-accent-light: #fef3c7;
            --pp-dark: #0f172a;
            --pp-slate: #334155;
            --pp-card-bg: #ffffff;
            --pp-bg: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--pp-bg);
            color: var(--pp-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .bg-pp-primary { background-color: var(--pp-primary) !important; color: #fff; }
        .bg-pp-dark { background-color: var(--pp-dark) !important; color: #fff; }
        .text-pp-primary { color: var(--pp-primary) !important; }
        .border-pp-primary { border-color: var(--pp-primary) !important; }

        .btn-pp-primary {
            background-color: var(--pp-primary);
            color: #ffffff;
            border: 1px solid var(--pp-primary);
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }
        .btn-pp-primary:hover {
            background-color: var(--pp-primary-dark);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
        }

        .btn-pp-outline {
            border: 1.5px solid var(--pp-primary);
            color: var(--pp-primary);
            font-weight: 600;
            background: transparent;
        }
        .btn-pp-outline:hover {
            background-color: var(--pp-primary);
            color: #ffffff;
        }

        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            transition: all 0.2s ease;
            background: #ffffff;
        }
        .card-custom:hover {
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }

        /* Demo Switcher Bar */
        .demo-bar {
            background: #0f172a;
            color: #94a3b8;
            font-size: 0.82rem;
            padding: 6px 0;
            border-bottom: 1px solid #1e293b;
        }
        .demo-pill {
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .demo-pill-petani { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .demo-pill-pedagang { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
        .demo-pill-umkm { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .demo-pill-admin { background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); }
        .demo-pill:hover { filter: brightness(1.2); }

        .navbar-main {
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border-bottom: 1px solid #e2e8f0;
        }

        .badge-risk-rendah { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; font-weight: 700; }
        .badge-risk-sedang { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; font-weight: 700; }
        .badge-risk-tinggi { background: #ffedd5; color: #9a3412; border: 1px solid #fdba74; font-weight: 700; }
        .badge-risk-sangat-tinggi { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-weight: 700; }

        .footer-main {
            margin-top: auto;
            background: #0f172a;
            color: #cbd5e1;
            padding: 3rem 0 1.5rem 0;
            border-top: 1px solid #1e293b;
        }

        .map-container {
            height: 380px;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #cbd5e1;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Demo Fast Switcher Bar -->
    <div class="demo-bar">
        <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-emerald-600 text-white px-2 py-1"><i class="bi bi-cpu me-1"></i> SIMULASI AKTOR</span>
                <span class="d-none d-md-inline text-slate-400">1-Click Fast Switch Demo:</span>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a href="{{ route('auth.demo', 'petani') }}" class="demo-pill demo-pill-petani" title="Login sebagai Petani (Pak Supardi - Indramayu)">
                    <i class="bi bi-flower1"></i> Petani (Pak Supardi)
                </a>
                <a href="{{ route('auth.demo', 'pedagang') }}" class="demo-pill demo-pill-pedagang" title="Login sebagai Pedagang Besar (H. Hendra - Cirebon)">
                    <i class="bi bi-truck"></i> Pedagang (H. Hendra)
                </a>
                <a href="{{ route('auth.demo', 'umkm') }}" class="demo-pill demo-pill-umkm" title="Login sebagai UMKM Pengolah (Bu Broto - Sambal)">
                    <i class="bi bi-shop"></i> UMKM (Bu Broto)
                </a>
                <a href="{{ route('auth.demo', 'admin') }}" class="demo-pill demo-pill-admin" title="Login sebagai Admin & Monev Bappenas">
                    <i class="bi bi-shield-check"></i> Admin (Bappenas)
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-main sticky-top py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <div class="bg-pp-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-seedling fs-5"></i>
                </div>
                <div>
                    <span class="fw-bold fs-5 text-dark" style="letter-spacing: -0.5px;">PANEN<span class="text-pp-primary">PULIH</span></span>
                    <small class="d-block text-muted" style="font-size: 0.65rem; margin-top: -3px; font-weight: 600;">PRE-HARVEST SURPLUS INTELLIGENCE</small>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house me-1"></i> Beranda
                        </a>
                    </li>

                    @auth
                        @if(auth()->user()->isPetani())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('petani.dashboard') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('petani.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard Petani
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('petani.rencana-panen.*') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('petani.rencana-panen.index') }}">
                                    <i class="bi bi-calendar4-week me-1"></i> Rencana Panen
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('petani.penawaran.*') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('petani.penawaran.index') }}">
                                    <i class="bi bi-tag me-1"></i> Penawaran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('petani.penyaluran.*') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('petani.penyaluran.index') }}">
                                    <i class="bi bi-clipboard2-check me-1"></i> Penyaluran & Dampak
                                </a>
                            </li>
                        @elseif(auth()->user()->isPembeli())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pembeli.dashboard') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('pembeli.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard Pembeli
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pembeli.kebutuhan.*') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('pembeli.kebutuhan.index') }}">
                                    <i class="bi bi-basket me-1"></i> Kebutuhan Saya
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pembeli.jelajah-surplus*') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('pembeli.jelajah-surplus') }}">
                                    <i class="bi bi-search me-1"></i> Jelajah Surplus Panen
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pembeli.penawaran.*') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('pembeli.penawaran.index') }}">
                                    <i class="bi bi-tag me-1"></i> Penawaran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pembeli.penyaluran.*') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('pembeli.penyaluran.index') }}">
                                    <i class="bi bi-truck me-1"></i> Riwayat Pengiriman
                                </a>
                            </li>
                        @elseif(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-graph-up-arrow me-1"></i> Command Center
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.komoditas.*') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('admin.komoditas.index') }}">
                                    <i class="bi bi-archive me-1"></i> Master Komoditas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('admin.users.index') }}">
                                    <i class="bi bi-people me-1"></i> Verifikasi Pengguna
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.laporan.sdg') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('admin.laporan.sdg') }}">
                                    <i class="bi bi-globe-americas me-1"></i> Laporan SDG 12 & 2
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.laporan.audit-log') ? 'active fw-bold text-pp-primary' : '' }}" href="{{ route('admin.laporan.audit-log') }}">
                                    <i class="bi bi-clock-history me-1"></i> Audit Trail
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary px-3">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-pp-primary px-3">Daftar Akun</a>
                    @else
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm border d-flex align-items-center gap-2 px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle text-pp-primary"></i>
                                <span class="fw-medium text-dark">{{ auth()->user()->name }}</span>
                                <span class="badge bg-secondary text-uppercase" style="font-size: 0.65rem;">{{ auth()->user()->role }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li class="px-3 py-2 text-muted border-bottom">
                                    <small class="d-block text-dark fw-bold">{{ auth()->user()->name }}</small>
                                    <small class="d-block">{{ auth()->user()->email }}</small>
                                    <small class="text-pp-primary fw-semibold">{{ auth()->user()->institution ?? ucfirst(auth()->user()->role) }}</small>
                                </li>
                                @if(auth()->user()->isPetani())
                                    <li><a class="dropdown-item py-2" href="{{ route('petani.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('petani.rencana-panen.create') }}"><i class="bi bi-plus-circle me-2"></i> Input Rencana Panen</a></li>
                                @elseif(auth()->user()->isPembeli())
                                    <li><a class="dropdown-item py-2" href="{{ route('pembeli.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('pembeli.kebutuhan.create') }}"><i class="bi bi-plus-circle me-2"></i> Input Kebutuhan</a></li>
                                @elseif(auth()->user()->isAdmin())
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i class="bi bi-graph-up me-2"></i> Dashboard Admin</a></li>
                                @endif
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger py-2">
                                            <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Global Alert Notifications -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill fs-5"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-x-circle me-1"></i> Mohon periksa kesalahan input berikut:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Main Page Content -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-main">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="bg-pp-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-seedling fs-5"></i>
                        </div>
                        <span class="fw-bold fs-5 text-white">PANEN<span class="text-pp-primary">PULIH</span></span>
                    </div>
                    <p class="text-slate-400 small mb-3">
                        Smart Pre-Harvest Surplus Intelligence & Distribution Platform. Mengantisipasi risiko surplus sebelum panen tiba, menghubungkan kebutuhan pedagang & UMKM pangan, dan memantau realisasi pangan terselamatkan.
                    </p>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25">
                            <i class="bi bi-check-circle me-1"></i> SDG 12: Konsumsi & Produksi Bertanggung Jawab
                        </span>
                        <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25">
                            <i class="bi bi-sun me-1"></i> SDG 2: Tanpa Kelaparan
                        </span>
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-bold mb-3">Fitur Platform</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 text-slate-400">
                        <li><a href="{{ route('home') }}#kalkulator" class="text-decoration-none text-slate-400">Kalkulator Surplus (S=H-P-K)</a></li>
                        <li><a href="{{ route('home') }}#risiko" class="text-decoration-none text-slate-400">Indikator Risiko Surplus</a></li>
                        <li><a href="{{ route('home') }}#pencocokan" class="text-decoration-none text-slate-400">Pencocokan Calon Pembeli</a></li>
                        <li><a href="{{ route('home') }}#dampak" class="text-decoration-none text-slate-400">Surplus-to-Impact Tracking</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3">
                    <h6 class="text-white fw-bold mb-3">Akses Cepat Pengguna</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 text-slate-400">
                        <li><a href="{{ route('auth.demo', 'petani') }}" class="text-decoration-none text-slate-400"><i class="bi bi-flower1 me-1"></i> Masuk sebagai Petani</a></li>
                        <li><a href="{{ route('auth.demo', 'pedagang') }}" class="text-decoration-none text-slate-400"><i class="bi bi-truck me-1"></i> Masuk sebagai Pedagang</a></li>
                        <li><a href="{{ route('auth.demo', 'umkm') }}" class="text-decoration-none text-slate-400"><i class="bi bi-shop me-1"></i> Masuk sebagai UMKM Pengolah</a></li>
                        <li><a href="{{ route('auth.demo', 'admin') }}" class="text-decoration-none text-slate-400"><i class="bi bi-shield-check me-1"></i> Masuk Dashboard Admin Bappenas</a></li>
                    </ul>
                </div>

                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-3">Landasan Riset</h6>
                    <p class="text-slate-400 small mb-2">
                        Didukung sintesis literatur Bappenas (2021), Oroski & da Silva (2023), Vishkaei & De Giovanni (2024), Fauziana dkk. (2023), Perdana dkk. (2023), dan Ihsan & Derosya (2024).
                    </p>
                    <div class="small text-muted">
                        Dokumen Konsep Proyek Web PANENPULIH &copy; 2026.
                    </div>
                </div>
            </div>

            <hr class="border-secondary border-opacity-25 my-3">

            <div class="d-flex flex-wrap justify-content-between align-items-center small text-slate-400">
                <div>&copy; 2026 PANENPULIH. Smart Pre-Harvest Surplus Intelligence & Distribution Platform.</div>
                <div>SDG 12 Sub-Theme &bull; Food Loss Prevention Prototype</div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('scripts')
</body>
</html>
