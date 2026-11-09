@extends('layouts.app')

@section('title', 'Detail Rencana Panen & Surplus Intelligence - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('petani.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('petani.rencana-panen.index') }}">Rencana Panen</a></li>
                    <li class="breadcrumb-item active">{{ $rencana->kode_rencana }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h2 class="fw-bold text-dark mb-0">{{ $rencana->komoditas->nama }}</h2>
                <span class="badge bg-light text-dark border">{{ $rencana->kode_rencana }}</span>
                <span class="badge {{ $rencana->status === 'aktif' ? 'bg-success bg-opacity-25 text-success' : 'bg-light text-muted border' }}">
                    Status: {{ ucfirst($rencana->status) }}
                </span>
            </div>
            <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i> {{ $rencana->lokasi }}</small>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('petani.rencana-panen.edit', $rencana->id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit Data
            </a>
            <a href="{{ route('petani.rencana-panen.index') }}" class="btn btn-light border btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- PRE-HARVEST SURPLUS INTELLIGENCE BANNER -->
    @php
        $analisis = $rencana->analisisSurplus;
        $risiko = $analisis?->kategori_risiko ?? 'Sedang';
        $badgeClass = match($risiko) {
            'Rendah' => 'badge-risk-rendah',
            'Sedang' => 'badge-risk-sedang',
            'Tinggi' => 'badge-risk-tinggi',
            'Sangat Tinggi' => 'badge-risk-sangat-tinggi',
            default => 'bg-secondary text-white'
        };
        $hariLagi = max(0, (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($rencana->tanggal_perkiraan_panen), false));
    @endphp

    <div class="card-custom p-4 bg-white mb-4 shadow-sm border-top border-4 border-emerald-600">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-emerald-100 text-emerald-800 border px-2 py-1 small fw-bold">
                        <i class="bi bi-cpu me-1"></i> PRE-HARVEST SURPLUS INTELLIGENCE
                    </span>
                    <span class="badge {{ $badgeClass }} px-2 py-1">Kategori: {{ $risiko }}</span>
                </div>
                <h4 class="fw-bold text-dark mb-1">
                    Potensi Surplus Belum Memiliki Pembeli: <span class="text-pp-primary">{{ number_format($rencana->potensi_surplus_kg, 0) }} kg</span>
                </h4>
                <p class="text-muted small mb-3">
                    Dari estimasi total panen <strong>{{ number_format($rencana->estimasi_hasil_kg, 0) }} kg</strong>, sebanyak <strong>{{ number_format($rencana->jumlah_terpesan_kg, 0) }} kg</strong> telah memiliki pembeli pasti dan <strong>{{ number_format($rencana->jumlah_alokasi_lain_kg, 0) }} kg</strong> dialokasikan untuk penggunaan lain.
                </p>

                <!-- 5 Risk Factors Progress -->
                <div class="row g-2 small">
                    <div class="col-6 col-md-4">
                        <div class="p-2 bg-light rounded border">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">0.25 × Proporsi Surplus (Sp)</span>
                            <strong class="text-dark">{{ $analisis?->skor_persentase_surplus ?? 0 }}%</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="p-2 bg-light rounded border">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">0.20 × Waktu Panen (W)</span>
                            <strong class="text-dark">{{ $hariLagi }} hari lagi</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="p-2 bg-light rounded border">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">0.25 × Kerentanan (U)</span>
                            <strong class="text-dark">{{ $analisis?->skor_kerentanan ?? 70 }} / 100</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-6">
                        <div class="p-2 bg-light rounded border">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">0.15 × Penyimpanan (G)</span>
                            <strong class="text-dark">{{ ucfirst(str_replace('_', ' ', $rencana->fasilitas_penyimpanan)) }}</strong>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="p-2 bg-light rounded border">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">0.15 × Keseimbangan Pasar (D)</span>
                            <strong class="text-dark">Skor Defisit: {{ $analisis?->skor_permintaan ?? 40 }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="p-4 rounded-4 bg-light border text-center">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Indeks Komposit Risiko Surplus (R)</small>
                    <h1 class="display-4 fw-extrabold text-dark my-1">{{ $analisis?->skor_risiko_total ?? 0 }}</h1>
                    <span class="badge {{ $badgeClass }} fs-6 px-3 py-1 mb-3">Tingkat Risiko: {{ $risiko }}</span>

                    <div class="p-2 bg-white rounded border small text-start text-dark">
                        <strong class="d-block text-pp-primary mb-1"><i class="bi bi-lightbulb-fill me-1"></i> Rekomendasi Tindakan:</strong>
                        {{ $analisis?->rekomendasi_tindakan ?? 'Prioritaskan koordinasi dengan pembeli terdekat sebelum H-3 panen.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- REKOMENDASI PEMBELI TERCOCOK (Novelty 2: Matching Algorithm) -->
    <div class="card-custom p-4 bg-white mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 small fw-bold">
                    <i class="bi bi-bullseye me-1"></i> NOVELTY 2: MATCHING ALGORITHM
                </span>
                <h4 class="fw-bold text-dark mt-1 mb-0">Rekomendasi Calon Pembeli Berbasis Risiko</h4>
                <small class="text-muted">
                    Diurutkan dari skor kecocokan tertinggi berdasarkan formula: <code>M = 0.30C + 0.20Q + 0.20L + 0.20T + 0.10J</code>
                </small>
            </div>
            <span class="badge bg-light text-muted border px-3 py-2 mt-2 mt-md-0">
                {{ $rekomendasiList->count() }} Calon Pembeli Tersedia
            </span>
        </div>

        @if($rekomendasiList->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-search fs-1 d-block mb-2"></i>
                <p class="mb-2">Belum ada kebutuhan pembeli yang cocok dengan komoditas ini di database.</p>
                <small>Sistem akan otomatis mencocokkan jika ada pedagang atau UMKM yang menambahkan kebutuhan komoditas {{ $rencana->komoditas->nama }}.</small>
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach($rekomendasiList as $rek)
                    @php
                        $kebutuhan = $rek->kebutuhanPembeli;
                        $pembeli = $kebutuhan->pembeli;
                        $jenis = ucfirst(str_replace('_', ' ', $pembeli->pembeliProfile->jenis_usaha ?? 'Pembeli'));
                        $isHighMatch = $rek->skor_total >= 75;
                    @endphp
                    <div class="p-3 rounded-4 border {{ $isHighMatch ? 'border-success bg-emerald-50 bg-opacity-25' : 'bg-light' }}">
                        <div class="row align-items-center g-3">
                            <div class="col-md-5">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge {{ $pembeli->role === 'umkm' ? 'bg-warning text-dark' : 'bg-primary' }}">
                                        {{ strtoupper($pembeli->role) }}
                                    </span>
                                    <span class="text-muted small">&bull; {{ $jenis }}</span>
                                    <span class="badge bg-white border text-dark ms-auto d-md-none">Skor: {{ $rek->skor_total }}%</span>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">{{ $pembeli->institution ?? $pembeli->name }}</h5>
                                <div class="small text-muted mb-2">
                                    <i class="bi bi-geo-alt text-danger me-1"></i> {{ $kebutuhan->lokasi }}
                                    <span class="fw-bold text-dark">({{ $rek->jarak_km }} km dari kebun Anda)</span>
                                </div>
                                <p class="small text-emerald-950 mb-0">
                                    <i class="bi bi-chat-quote-fill text-success me-1"></i> {{ $rek->alasan_rekomendasi }}
                                </p>
                            </div>

                            <div class="col-md-4">
                                <div class="p-2 bg-white rounded border small">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Kebutuhan Pembeli (Q):</span>
                                        <strong>{{ number_format($kebutuhan->jumlah_kg, 0) }} kg</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Grade Diterima (J):</span>
                                        <strong class="text-uppercase">{{ $kebutuhan->grade_diterima }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Jendela Tanggal (T):</span>
                                        <span>{{ \Carbon\Carbon::parse($kebutuhan->tanggal_mulai_butuh)->format('d M') }} - {{ \Carbon\Carbon::parse($kebutuhan->batas_tanggal_butuh)->format('d M') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between border-top pt-1">
                                        <span>Budget Pembeli:</span>
                                        <strong class="text-success">Rp {{ number_format($kebutuhan->budget_per_kg, 0, ',', '.') }}/kg</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 text-md-end">
                                <div class="d-none d-md-block mb-2">
                                    <span class="badge {{ $isHighMatch ? 'bg-success' : 'bg-secondary' }} px-3 py-2 fs-6">
                                        Kecocokan: {{ $rek->skor_total }}%
                                    </span>
                                </div>
                                <button type="button" class="btn btn-pp-primary btn-sm w-100 fw-semibold"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalPenawaran"
                                        data-kebutuhan-id="{{ $kebutuhan->id }}"
                                        data-pembeli-name="{{ $pembeli->institution ?? $pembeli->name }}"
                                        data-jumlah="{{ min($rencana->potensi_surplus_kg, $kebutuhan->jumlah_kg) }}"
                                        data-harga="{{ $kebutuhan->budget_per_kg ?? $rencana->ekspektasi_harga_per_kg ?? 30000 }}"
                                        data-panen="{{ $rencana->tanggal_perkiraan_panen }}">
                                    <i class="bi bi-send me-1"></i> Ajukan Penawaran
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- PENAWARAN & KONTRAK AKTIF PADA RENCANA PANEN INI -->
    <div class="card-custom p-4 bg-white">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-tag-fill text-pp-primary me-2"></i> Penawaran & Kesepakatan Terkait</h5>

        @if($rencana->penawaran->isEmpty())
            <p class="text-muted small mb-0">Belum ada penawaran pembelian aktif pada rencana panen ini.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Pembeli</th>
                            <th>Jumlah</th>
                            <th>Harga / kg</th>
                            <th>Total</th>
                            <th>Pengambilan</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rencana->penawaran as $pen)
                            <tr>
                                <td><span class="badge bg-light text-dark border">{{ $pen->kode_penawaran }}</span></td>
                                <td>
                                    <strong class="d-block">{{ $pen->pembeli->institution ?? $pen->pembeli->name }}</strong>
                                    <small class="text-muted">{{ ucfirst($pen->inisiator_role) }}</small>
                                </td>
                                <td><strong>{{ number_format($pen->jumlah_kg, 0) }} kg</strong></td>
                                <td>Rp {{ number_format($pen->harga_per_kg, 0, ',', '.') }}</td>
                                <td class="fw-bold text-success">Rp {{ number_format($pen->total_harga, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($pen->tanggal_pengambilan)->isoFormat('D MMM Y') }}</td>
                                <td>
                                    <span class="badge {{ $pen->status === 'disetujui' ? 'bg-success' : ($pen->status === 'selesai' ? 'bg-primary' : 'bg-warning text-dark') }}">
                                        {{ ucfirst(str_replace('_', ' ', $pen->status)) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if($pen->status === 'menunggu_konfirmasi' && $pen->inisiator_role !== 'petani')
                                        <form action="{{ route('petani.penawaran.terima', $pen->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-success py-1 px-2">Terima</button>
                                        </form>
                                    @elseif($pen->status === 'disetujui')
                                        <a href="{{ route('petani.penyaluran.formCatat', $pen->id) }}" class="btn btn-xs btn-pp-primary py-1 px-2">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Catat Penyaluran
                                        </a>
                                    @elseif($pen->status === 'selesai')
                                        <span class="badge bg-light text-success border"><i class="bi bi-check-all"></i> Tersalurkan</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Modal Ajukan Penawaran ke Pembeli -->
<div class="modal fade" id="modalPenawaran" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('petani.penawaran.ajukan') }}" method="POST">
                @csrf
                <input type="hidden" name="rencana_panen_id" value="{{ $rencana->id }}">
                <input type="hidden" name="kebutuhan_pembeli_id" id="modalKebutuhanId">

                <div class="modal-header bg-pp-primary text-white">
                    <h5 class="modal-title fw-bold fs-6"><i class="bi bi-send me-1"></i> Ajukan Penawaran Surplus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Calon Pembeli Tujuan</label>
                        <input type="text" id="modalPembeliName" class="form-control" readonly>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Jumlah Surplus Ditawarkan (kg) <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_kg" id="modalJumlahKg" class="form-control" required min="1" max="{{ $rencana->potensi_surplus_kg }}">
                            <small class="text-muted">Maks: {{ number_format($rencana->potensi_surplus_kg, 0) }} kg</small>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Harga Penawaran / kg <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_per_kg" id="modalHargaKg" class="form-control" required min="100">
                            </div>
                        </div>

                        <div class="col-6">
                            <label class="form-label small fw-bold">Tanggal Pengambilan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_pengambilan" id="modalTglAmbil" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Metode Penyaluran <span class="text-danger">*</span></label>
                            <select name="metode_pengiriman" class="form-select" required>
                                <option value="diambil_pembeli" selected>Diambil Pembeli</option>
                                <option value="dikirim_petani">Dikirim Petani</option>
                                <option value="jasa_angkut">Jasa Angkut Bersama</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Pesan / Catatan Tambahan</label>
                            <textarea name="catatan" class="form-control" rows="2" placeholder="Informasi kesiapan panen, akses truk, atau opsi negosiasi..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-pp-primary btn-sm fw-semibold">Kirim Penawaran ke Pembeli</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalPenawaran');
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            document.getElementById('modalKebutuhanId').value = button.getAttribute('data-kebutuhan-id');
            document.getElementById('modalPembeliName').value = button.getAttribute('data-pembeli-name');
            document.getElementById('modalJumlahKg').value = button.getAttribute('data-jumlah');
            document.getElementById('modalHargaKg').value = button.getAttribute('data-harga');
            document.getElementById('modalTglAmbil').value = button.getAttribute('data-panen');
        });
    });
</script>
@endpush
