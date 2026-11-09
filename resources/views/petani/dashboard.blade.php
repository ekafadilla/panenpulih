@extends('layouts.app')

@section('title', 'Dashboard Petani - PANENPULIH')

@section('content')
<div class="container pb-5">
    <!-- Header Petani -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                    <i class="bi bi-flower1 me-1"></i> PANEL PETANI
                </span>
                <span class="text-muted small">{{ $user->institution ?? 'Kelompok Tani' }}</span>
            </div>
            <h2 class="fw-bold text-dark mt-1 mb-0">Selamat Datang, {{ $user->name }}!</h2>
            <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i> {{ $user->address }}</small>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('petani.rencana-panen.create') }}" class="btn btn-pp-primary d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-plus-circle"></i> Input Rencana Panen Baru
            </a>
            <a href="{{ route('petani.penawaran.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <i class="bi bi-inbox"></i> Kelola Penawaran
            </a>
        </div>
    </div>

    <!-- Alert Risiko Mendesak (Jika ada) -->
    @if($peringatanRisiko->isNotEmpty())
        <div class="alert alert-warning border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-start gap-3">
            <div class="bg-warning text-dark p-2 rounded-3 fs-4">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1 text-dark">Perhatian: Terdeteksi {{ $peringatanRisiko->count() }} Rencana Panen Berisiko Surplus Tinggi!</h6>
                <p class="small text-muted mb-2">
                    Hasil panen Anda mendekati tanggal petik dan memiliki potensi kelebihan yang belum memiliki pembeli terkonfirmasi. Segera tinjau rekomendasi pedagang dan UMKM lokal.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($peringatanRisiko as $itemRisiko)
                        <a href="{{ route('petani.rencana-panen.show', $itemRisiko->id) }}" class="btn btn-sm btn-dark">
                            {{ $itemRisiko->komoditas->nama }} ({{ number_format($itemRisiko->potensi_surplus_kg, 0) }} kg) &bull; Skor: {{ $itemRisiko->analisisSurplus?->skor_risiko_total }} <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Kartu Metrik Utama -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card-custom p-3">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Total Rencana Panen</small>
                    <div class="bg-light p-2 rounded-2 text-dark"><i class="bi bi-calendar-check"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $totalRencana }}</h3>
                <small class="text-muted">{{ $rencanaAktif->count() }} komoditas aktif</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card-custom p-3">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Potensi Surplus Terdeteksi</small>
                    <div class="bg-light p-2 rounded-2 text-warning"><i class="bi bi-graph-up"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($metrikPetani['total_surplus_kg'], 0, ',', '.') }}</h3>
                <small class="text-muted">kg hasil pre-harvest</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card-custom p-3">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Surplus Terserap (Tersalurkan)</small>
                    <div class="bg-light p-2 rounded-2 text-success"><i class="bi bi-check-circle"></i></div>
                </div>
                <h3 class="fw-bold text-pp-primary mb-0">{{ number_format($metrikPetani['total_terserap_kg'], 0, ',', '.') }}</h3>
                <small class="text-success fw-semibold"><i class="bi bi-shield-check me-1"></i> {{ $metrikPetani['tingkat_penyerapan'] }}% terserap</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card-custom p-3">
                <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                    <small class="fw-semibold">Pendapatan Terselamatkan</small>
                    <div class="bg-light p-2 rounded-2 text-primary"><i class="bi bi-cash-stack"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0">Rp {{ number_format($metrikPetani['total_pendapatan_rp'] / 1000000, 1, ',', '.') }} M</h3>
                <small class="text-muted">dari surplus tersalurkan</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Rencana Panen Aktif -->
        <div class="col-lg-8">
            <div class="card-custom p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar4-week text-pp-primary me-2"></i> Rencana Panen & Surplus Aktif</h5>
                    <a href="{{ route('petani.rencana-panen.index') }}" class="btn btn-sm btn-outline-secondary">Semua Rencana</a>
                </div>

                @if($rencanaAktif->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                        <p class="mb-3">Belum ada rencana panen terdaftar.</p>
                        <a href="{{ route('petani.rencana-panen.create') }}" class="btn btn-pp-primary btn-sm">Mulai Buat Rencana Panen</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th>Komoditas</th>
                                    <th>Estimasi (H)</th>
                                    <th>Terpesan (P)</th>
                                    <th>Surplus (S)</th>
                                    <th>Panen</th>
                                    <th>Tingkat Risiko</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rencanaAktif as $rencana)
                                    @php
                                        $hariLagi = max(0, (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($rencana->tanggal_perkiraan_panen), false));
                                        $risiko = $rencana->analisisSurplus?->kategori_risiko ?? 'Sedang';
                                        $badgeClass = match($risiko) {
                                            'Rendah' => 'badge-risk-rendah',
                                            'Sedang' => 'badge-risk-sedang',
                                            'Tinggi' => 'badge-risk-tinggi',
                                            'Sangat Tinggi' => 'badge-risk-sangat-tinggi',
                                            default => 'bg-secondary text-white'
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong class="d-block text-dark">{{ $rencana->komoditas->nama }}</strong>
                                            <small class="text-muted">{{ $rencana->kode_rencana }}</small>
                                        </td>
                                        <td>{{ number_format($rencana->estimasi_hasil_kg, 0) }} kg</td>
                                        <td class="text-muted">{{ number_format($rencana->jumlah_terpesan_kg, 0) }} kg</td>
                                        <td>
                                            <span class="fw-bold text-pp-primary">{{ number_format($rencana->potensi_surplus_kg, 0) }} kg</span>
                                        </td>
                                        <td>
                                            <span class="d-block text-dark fw-semibold">{{ \Carbon\Carbon::parse($rencana->tanggal_perkiraan_panen)->isoFormat('D MMM Y') }}</span>
                                            <small class="badge bg-light text-muted border">{{ $hariLagi }} hari lagi</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $badgeClass }} px-2 py-1">
                                                {{ $risiko }} ({{ $rencana->analisisSurplus?->skor_risiko_total }})
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('petani.rencana-panen.show', $rencana->id) }}" class="btn btn-sm btn-outline-success py-1 px-2" title="Lihat Rekomendasi Pembeli">
                                                <i class="bi bi-people-fill me-1"></i> Cocokkan
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Riwayat Penyaluran Terakhir -->
            <div class="card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-check2-circle text-success me-2"></i> Realisasi Penyaluran Surplus</h5>
                    <a href="{{ route('petani.penyaluran.index') }}" class="btn btn-sm btn-outline-secondary">Riwayat Lengkap</a>
                </div>

                @if($penyaluranTerbaru->isEmpty())
                    <p class="text-muted small mb-0">Belum ada realisasi penyaluran yang dicatat.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Komoditas</th>
                                    <th>Pembeli</th>
                                    <th>Jumlah Realisasi</th>
                                    <th>Total Nilai</th>
                                    <th>Penyerapan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penyaluranTerbaru as $penyaluran)
                                    <tr>
                                        <td><span class="badge bg-light text-dark border">{{ $penyaluran->kode_penyaluran }}</span></td>
                                        <td class="fw-semibold">{{ $penyaluran->penawaran->rencanaPanen->komoditas->nama }}</td>
                                        <td>{{ $penyaluran->penawaran->pembeli->institution ?? $penyaluran->penawaran->pembeli->name }}</td>
                                        <td><strong>{{ number_format($penyaluran->jumlah_realisasi_kg, 0) }} kg</strong></td>
                                        <td class="text-success fw-bold">Rp {{ number_format($penyaluran->harga_total_realisasi, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-success bg-opacity-25 text-success">
                                                {{ $penyaluran->tingkat_penyerapan_persen }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Samping: Penawaran Masuk dari Pembeli -->
        <div class="col-lg-4">
            <div class="card-custom p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-inbox text-primary me-2"></i> Pengajuan Masuk ({{ $penawaranMasuk->count() }})</h6>
                    <a href="{{ route('petani.penawaran.index') }}" class="small text-pp-primary text-decoration-none">Semua</a>
                </div>

                @if($penawaranMasuk->isEmpty())
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-chat-dots fs-3 d-block mb-2"></i>
                        Tidak ada pengajuan pembelian baru yang menunggu konfirmasi saat ini.
                    </div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($penawaranMasuk as $p)
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-white text-dark border">{{ $p->kode_penawaran }}</span>
                                    <span class="badge bg-warning bg-opacity-25 text-warning text-dark">Menunggu Persetujuan</span>
                                </div>
                                <div class="fw-bold text-dark">{{ $p->pembeli->institution ?? $p->pembeli->name }}</div>
                                <small class="text-muted d-block mb-2"><i class="bi bi-box-seam me-1"></i> Komoditas: {{ $p->rencanaPanen->komoditas->nama }}</small>

                                <div class="bg-white p-2 rounded border small mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Jumlah Penawaran:</span>
                                        <strong>{{ number_format($p->jumlah_kg, 0) }} kg</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Harga Ditawarkan:</span>
                                        <strong class="text-success">Rp {{ number_format($p->harga_per_kg, 0, ',', '.') }}/kg</strong>
                                    </div>
                                    <div class="d-flex justify-content-between border-top pt-1 mt-1">
                                        <span>Total:</span>
                                        <strong class="text-dark">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <form action="{{ route('petani.penawaran.terima', $p->id) }}" method="POST" class="w-100">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success w-100 fw-semibold">
                                            <i class="bi bi-check-lg me-1"></i> Terima
                                        </button>
                                    </form>
                                    <form action="{{ route('petani.penawaran.tolak', $p->id) }}" method="POST" class="w-100">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Tips Algoritma Mitigasi Pre-Harvest -->
            <div class="card-custom p-4 bg-emerald-50 bg-opacity-50 border-emerald-200">
                <h6 class="fw-bold text-emerald-900 mb-2"><i class="bi bi-lightbulb text-warning me-2"></i> Panduan Surplus Intelligence</h6>
                <ul class="ps-3 small text-emerald-800 mb-0 d-flex flex-column gap-2">
                    <li>Perbarui estimasi panen ($H$) pada H-7 dan H-3 untuk meningkatkan akurasi rekomendasi pembeli.</li>
                    <li>Untuk komoditas mudah rusak (cabai & tomat), pertimbangkan penyerapan oleh UMKM pengolah lokal yang menerima grade B atau imperfect produce.</li>
                    <li>Gunakan kemasan berventilasi untuk menekan laju respirasi dan pembusukan saat menunggu transportasi.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
