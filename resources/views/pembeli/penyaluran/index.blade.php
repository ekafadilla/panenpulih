@extends('layouts.app')

@section('title', 'Penerimaan Hasil Panen & Konfirmasi - PANENPULIH')

@section('content')
<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('pembeli.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Riwayat Penerimaan</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Riwayat Penyerapan & Penerimaan Pangan</h2>
            <small class="text-muted">Konfirmasi barang tiba dan berikan ulasan kualitas produk hasil panen</small>
        </div>
    </div>

    <div class="card-custom p-4 bg-white">
        @if($penyaluranList->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-truck fs-1 d-block mb-2"></i>
                <h5 class="fw-bold text-dark">Belum Ada Pengiriman yang Selesai</h5>
                <p class="small text-muted mb-0">Pengiriman akan muncul setelah petani mencatat realisasi serah terima hasil panen.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Penyaluran</th>
                            <th>Tanggal Penyerahan</th>
                            <th>Komoditas</th>
                            <th>Petani Pengirim</th>
                            <th>Kuantitas Diterima</th>
                            <th>Nilai Transaksi</th>
                            <th>Kualitas Produk</th>
                            <th>Status & Ulasan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penyaluranList as $peny)
                            <tr>
                                <td><span class="badge bg-light text-dark border">{{ $peny->kode_penyaluran }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($peny->tanggal_penyaluran)->isoFormat('D MMM Y') }}</td>
                                <td><strong>{{ $peny->penawaran->rencanaPanen->komoditas->nama }}</strong></td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $peny->penawaran->petani->name }}</span>
                                    <small class="d-block text-muted">{{ $peny->penawaran->petani->address }}</small>
                                </td>
                                <td><strong class="text-pp-primary fs-6">{{ number_format($peny->jumlah_realisasi_kg, 0) }} kg</strong></td>
                                <td class="fw-bold text-dark">Rp {{ number_format($peny->harga_total_realisasi, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ ucfirst(str_replace('_', ' ', $peny->status_kualitas)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($peny->status_verifikasi === 'selesai' && $peny->rating_pembeli)
                                        <div class="text-warning">
                                            @for($i=1; $i<=$peny->rating_pembeli; $i++)
                                                <i class="bi bi-star-fill"></i>
                                            @endfor
                                        </div>
                                        <small class="text-muted d-block">{{ Str::limit($peny->ulasan_pembeli, 25) }}</small>
                                    @else
                                        <span class="badge bg-warning bg-opacity-25 text-warning text-dark border border-warning">
                                            Menunggu Ulasan
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($peny->status_verifikasi !== 'selesai' || !$peny->rating_pembeli)
                                        <button type="button" class="btn btn-sm btn-pp-primary py-1 px-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalReview"
                                                data-id="{{ $peny->id }}"
                                                data-kode="{{ $peny->kode_penyaluran }}"
                                                data-petani="{{ $peny->penawaran->petani->name }}"
                                                data-kg="{{ number_format($peny->jumlah_realisasi_kg, 0) }} kg {{ $peny->penawaran->rencanaPanen->komoditas->nama }}">
                                            <i class="bi bi-star me-1"></i> Konfirmasi & Nilai
                                        </button>
                                    @else
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success">
                                            <i class="bi bi-check2-all me-1"></i> Selesai
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $penyaluranList->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Konfirmasi & Rating Ulasan -->
<div class="modal fade" id="modalReview" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form id="formReview" method="POST">
                @csrf
                <div class="modal-header bg-pp-primary text-white">
                    <h5 class="modal-title fw-bold fs-6"><i class="bi bi-check2-square me-1"></i> Konfirmasi Penerimaan & Ulasan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 small">
                        <div class="text-muted">Penyaluran: <strong id="modalKodePenyaluran" class="text-dark"></strong></div>
                        <div class="text-muted">Komoditas: <strong id="modalInfoBarang" class="text-dark"></strong></div>
                        <div class="text-muted">Petani: <strong id="modalNamaPetani" class="text-dark"></strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Penilaian Kepuasan Kualitas Panen (1-5 Bintang) <span class="text-danger">*</span></label>
                        <select name="rating_pembeli" class="form-select" required>
                            <option value="5" selected>⭐⭐⭐⭐⭐ - Sangat Memuaskan & Segar</option>
                            <option value="4">⭐⭐⭐⭐ - Baik / Sesuai Ekspektasi</option>
                            <option value="3">⭐⭐⭐ - Cukup / Ada Sedikit Susut</option>
                            <option value="2">⭐⭐ - Kurang / Banyak Cacat</option>
                            <option value="1">⭐ - Sangat Kurang</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Ulasan / Masukan untuk Petani</label>
                        <textarea name="ulasan_pembeli" class="form-control" rows="3" placeholder="Tuliskan ulasan mengenai kesegaran barang, kemasan, atau ketepatan waktu serah terima..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-pp-primary btn-sm fw-semibold">Kirim Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalReview');
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const kode = button.getAttribute('data-kode');
            const petani = button.getAttribute('data-petani');
            const kg = button.getAttribute('data-kg');

            document.getElementById('modalKodePenyaluran').textContent = kode;
            document.getElementById('modalInfoBarang').textContent = kg;
            document.getElementById('modalNamaPetani').textContent = petani;

            document.getElementById('formReview').action = '/pembeli/penyaluran/' + id + '/konfirmasi';
        });
    });
</script>
@endpush
