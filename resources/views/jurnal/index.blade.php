@extends('layouts.app')

@section('title', 'Jurnal Mengajar')

@section('content')
<div class="container-fluid">

    {{-- Alert --}}
    @if(session('success'))
        <div class="app-alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="app-alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-book me-2 text-green"></i>Jurnal Mengajar
        </div>
        <div class="page-subtitle">Kelola semua jurnal mengajar kamu</div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="stat-card hadir">
                <div class="stat-label">
                    <i class="fas fa-book me-1"></i>Total Jurnal
                </div>
                <div class="stat-value" style="color: var(--color-hadir);">
                    {{ $totalJurnal }}
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="stat-card hadir">
                <div class="stat-label">
                    <i class="fas fa-check-circle me-1"></i>Dinilai
                </div>
                <div class="stat-value" style="color: var(--color-hadir);">
                    {{ $totalDinilai }}
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="stat-card alpha">
                <div class="stat-label">
                    <i class="fas fa-redo me-1"></i>Revisi
                </div>
                <div class="stat-value" style="color: var(--color-alpha);">
                    {{ $totalRevisi }}
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="stat-card izin">
                <div class="stat-label">
                    <i class="fas fa-clock me-1"></i>Pending
                </div>
                <div class="stat-value" style="color: var(--color-izin);">
                    {{ $totalPending }}
                </div>
            </div>
        </div>
    </div>

    {{-- Rata-rata Nilai --}}
    @if($rataRata)
        <div class="app-alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="fas fa-star"></i>
            <span><strong>Rata-rata Nilai:</strong> {{ number_format($rataRata, 2) }}</span>
        </div>
    @endif

    {{-- Notifikasi --}}
    @if($notifikasi && $notifikasi->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-bell text-green"></i>
                <span>Notifikasi Jurnal Dinilai</span>
            </div>
            <div class="card-body p-0">
                @foreach($notifikasi as $item)
                    <div class="d-flex justify-content-between align-items-center px-4 py-3"
                         style="border-bottom: 1px solid var(--gray-100);">
                        <div>
                            <div style="font-weight: 600; font-size: 13.5px; color: var(--gray-800);">
                                {{ $item->mata_pelajaran }}
                            </div>
                            <div style="font-size: 12px; color: var(--gray-400);">
                                {{ $item->kelas }}
                            </div>
                        </div>
                        <span class="badge-status badge-hadir">
                            <i class="fas fa-star" style="font-size: 9px;"></i>
                            Nilai {{ $item->evaluasi->nilai ?? '—' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Filter + Tambah --}}
    <div class="card mb-4">
        <div class="card-body" style="padding: 16px 20px;">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari mata pelajaran / kelas"
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                        <option value="dinilai"  {{ request('status') === 'dinilai'  ? 'selected' : '' }}>Dinilai</option>
                        <option value="revisi"   {{ request('status') === 'revisi'   ? 'selected' : '' }}>Revisi</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('jurnal.create') }}" class="btn btn-primary w-100"
                       style="background: linear-gradient(135deg, var(--green-700), var(--green-600)) !important;">
                        <i class="fas fa-plus-circle me-1"></i> Tambah
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-app mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th style="width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($jurnal as $item)
                        <tr>
                            <td style="font-family: 'DM Mono', monospace; font-size: 12px;">
                                {{ $item->tanggal->format('d M Y') }}
                            </td>
                            <td style="font-family: 'DM Mono', monospace; font-size: 12px;">
                                {{ $item->jam_pelajaran }}
                            </td>
                            <td>
                                <a href="{{ route('jurnal.show', $item->id) }}"
                                   style="font-weight: 600; color: var(--green-600); text-decoration: none;">
                                    {{ $item->mata_pelajaran }}
                                </a>
                            </td>
                            <td style="color: var(--gray-600);">{{ $item->kelas }}</td>
                            <td>
                                @if($item->status === 'pending')
                                    <span class="badge-status badge-izin">
                                        <i class="fas fa-clock" style="font-size: 9px;"></i> Pending
                                    </span>
                                @elseif($item->status === 'dinilai')
                                    <span class="badge-status badge-hadir">
                                        <i class="fas fa-check-circle" style="font-size: 9px;"></i> Dinilai
                                    </span>
                                @elseif($item->status === 'revisi')
                                    <span class="badge-status badge-alpha">
                                        <i class="fas fa-redo" style="font-size: 9px;"></i> Revisi
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('jurnal.show', $item->id) }}"
                                       class="btn btn-sm"
                                       style="background: var(--green-50); color: var(--green-700); border: 1px solid var(--green-200); border-radius: var(--radius-sm); font-size: 12px;"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($item->status !== 'dinilai')
                                        <a href="{{ route('jurnal.edit', $item->id) }}"
                                           class="btn btn-sm"
                                           style="background: var(--green-50); color: var(--green-700); border: 1px solid var(--green-200); border-radius: var(--radius-sm); font-size: 12px;"
                                           title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-hapus-trigger"
                                                style="background: #fef2f2; color: var(--color-alpha); border: 1px solid #fecaca; border-radius: var(--radius-sm); font-size: 12px;"
                                                data-id="{{ $item->id }}"
                                                data-mapel="{{ $item->mata_pelajaran }}"
                                                data-kelas="{{ $item->kelas }}"
                                                data-tanggal="{{ $item->tanggal->format('d M Y') }}"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <span style="color: var(--gray-300); font-size: 12px;" class="align-self-center ps-1">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center"
                                style="padding: 48px; color: var(--gray-400);">
                                <i class="fas fa-book-open"
                                   style="font-size: 32px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                                Belum ada jurnal
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($jurnal->hasPages())
            <div class="d-flex justify-content-end p-3"
                 style="border-top: 1px solid var(--gray-100);">
                {{ $jurnal->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

{{-- Modal Hapus --}}
<div class="modal fade" id="modalHapusIndex" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content"
             style="border: none; border-radius: var(--radius-xl); overflow: hidden; box-shadow: var(--shadow-lg);">

            {{-- Header Modal --}}
            <div style="background: var(--color-alpha); padding: 28px 24px 22px; text-align: center;">
                <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;">
                    <i class="fas fa-trash" style="font-size: 24px; color: #fff;"></i>
                </div>
                <h5 style="color: #fff; font-weight: 800; font-size: 18px; margin: 0;">Hapus Jurnal?</h5>
                <p style="color: rgba(255,255,255,0.75); font-size: 13px; margin: 6px 0 0;">
                    Tindakan ini tidak bisa dibatalkan
                </p>
            </div>

            {{-- Body Modal --}}
            <div style="padding: 20px 24px 4px; background: #fff;">
                <p style="font-size: 13.5px; color: var(--gray-500); text-align: center; margin-bottom: 12px;">
                    Kamu akan menghapus jurnal berikut:
                </p>
                <div style="background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: var(--radius-md); padding: 14px 16px; margin-bottom: 8px;">
                    <div style="font-size: 11px; color: var(--gray-400); margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.05em;">
                        Mata Pelajaran / Kelas
                    </div>
                    <div id="modal-hapus-mapel"
                         style="font-size: 15px; font-weight: 700; color: var(--gray-800);"></div>
                    <div id="modal-hapus-tanggal"
                         style="font-size: 12.5px; color: var(--gray-400); margin-top: 5px;">
                        <i class="fas fa-calendar me-1"></i>
                    </div>
                </div>
            </div>

            {{-- Footer Modal --}}
            <div style="padding: 16px 24px 24px; background: #fff; display: flex; gap: 10px;">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <form id="form-hapus-index" method="POST" class="flex-fill">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn w-100"
                            style="background: var(--color-alpha); color: #fff; border: none; border-radius: var(--radius-md); font-weight: 600;">
                        <i class="fas fa-trash me-1"></i> Ya, Hapus
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.btn-hapus-trigger').forEach(btn => {
    btn.addEventListener('click', function () {
        const id    = this.dataset.id;
        const mapel = this.dataset.mapel;
        const kelas = this.dataset.kelas;
        const tgl   = this.dataset.tanggal;

        document.getElementById('modal-hapus-mapel').textContent  = mapel + ' — ' + kelas;
        document.getElementById('modal-hapus-tanggal').innerHTML  =
            '<i class="fas fa-calendar me-1"></i>' + tgl;
        document.getElementById('form-hapus-index').action = '/guru/jurnal/' + id;

        new bootstrap.Modal(document.getElementById('modalHapusIndex')).show();
    });
});
</script>
@endpush

@endsection