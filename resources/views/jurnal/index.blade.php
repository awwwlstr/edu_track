@extends('layouts.app')

@section('title', 'Jurnal Mengajar')

@section('content')

<div class="p-4 fade-up">

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success mb-4">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-4">
            <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="page-header mb-4">
        <h4>Jurnal Mengajar</h4>
        <small>Kelola semua jurnal mengajar kamu</small>
    </div>

    {{-- STATISTIK --}}
    <div class="row g-3 mb-4 stagger">

        <div class="col-sm-6 col-md-3">
            <div class="stat-box">
                <div style="width:40px;height:40px;background:var(--brand-light);border-radius:var(--r);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                    <i class="bi bi-journal-text" style="font-size:17px;color:var(--brand);"></i>
                </div>
                <div class="stat-title">Total Jurnal</div>
                <div class="stat-value">{{ $totalJurnal }}</div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="stat-box">
                <div style="width:40px;height:40px;background:var(--brand-light);border-radius:var(--r);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                    <i class="bi bi-check-circle" style="font-size:17px;color:var(--brand);"></i>
                </div>
                <div class="stat-title">Dinilai</div>
                <div class="stat-value">{{ $totalDinilai }}</div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="stat-box">
                <div style="width:40px;height:40px;background:var(--danger-light);border-radius:var(--r);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                    <i class="bi bi-arrow-repeat" style="font-size:17px;color:var(--danger);"></i>
                </div>
                <div class="stat-title">Revisi</div>
                <div class="stat-value" style="color:var(--danger);">{{ $totalRevisi }}</div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="stat-box">
                <div style="width:40px;height:40px;background:var(--warning-light);border-radius:var(--r);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                    <i class="bi bi-clock-history" style="font-size:17px;color:var(--warning);"></i>
                </div>
                <div class="stat-title">Pending</div>
                <div class="stat-value" style="color:var(--warning);">{{ $totalPending }}</div>
            </div>
        </div>

    </div>

    {{-- RATA-RATA NILAI --}}
    @if($rataRata)
        <div class="alert alert-success mb-4">
            <i class="bi bi-star-fill"></i>
            <strong>Rata-rata Nilai:</strong> {{ number_format($rataRata, 2) }}
        </div>
    @endif

    {{-- NOTIFIKASI --}}
    @if($notifikasi && $notifikasi->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-bell-fill me-2 text-success"></i>Notifikasi Jurnal Dinilai
            </div>
            <div class="card-body" style="padding-top:8px !important;padding-bottom:8px !important;">
                @foreach($notifikasi as $item)
                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid var(--border);">
                        <div>
                            <div style="font-weight:600;font-size:13.5px;">{{ $item->mata_pelajaran }}</div>
                            <div style="font-size:12px;color:var(--text-muted);">{{ $item->kelas }}</div>
                        </div>
                        <span class="badge bg-success">Nilai {{ $item->evaluasi->nilai ?? '—' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- FILTER + TAMBAH --}}
    <div class="card mb-4">
        <div class="card-body">
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
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
                <div class="col-md-2 text-end">
                    <a href="{{ route('jurnal.create') }}" class="btn btn-success w-100">
                        <i class="bi bi-plus-circle"></i> Tambah
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th style="width:130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($jurnal as $item)
                    <tr>
                        <td>{{ $item->tanggal->format('d M Y') }}</td>
                        <td>{{ $item->jam_pelajaran }}</td>
                        <td>
                            <a href="{{ route('jurnal.show', $item->id) }}"
                               style="font-weight:600;color:var(--brand);">
                                {{ $item->mata_pelajaran }}
                            </a>
                        </td>
                        <td>{{ $item->kelas }}</td>
                        <td>
                            @if($item->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($item->status === 'dinilai')
                                <span class="badge bg-success">Dinilai</span>
                            @elseif($item->status === 'revisi')
                                <span class="badge bg-danger">Revisi</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('jurnal.show', $item->id) }}"
                                   class="btn btn-sm btn-outline-primary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($item->status !== 'dinilai')
                                    <a href="{{ route('jurnal.edit', $item->id) }}"
                                       class="btn btn-sm btn-outline-success" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger btn-hapus-trigger"
                                            data-id="{{ $item->id }}"
                                            data-mapel="{{ $item->mata_pelajaran }}"
                                            data-kelas="{{ $item->kelas }}"
                                            data-tanggal="{{ $item->tanggal->format('d M Y') }}"
                                            title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @else
                                    <span class="text-muted small align-self-center ps-1">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                            Belum ada jurnal
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($jurnal->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $jurnal->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>


{{-- MODAL HAPUS DARI TABEL --}}
<div class="modal fade" id="modalHapusIndex" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content" style="border:none;border-radius:var(--r-xl);overflow:hidden;box-shadow:var(--shadow-lg);">

            <div style="background:var(--danger);padding:28px 24px 22px;text-align:center;">
                <div style="width:60px;height:60px;background:rgba(255,255,255,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <i class="bi bi-trash3-fill" style="font-size:26px;color:#fff;"></i>
                </div>
                <h5 style="color:#fff;font-weight:800;font-size:18px;margin:0;letter-spacing:-0.3px;">Hapus Jurnal?</h5>
                <p style="color:rgba(255,255,255,0.75);font-size:13px;margin:6px 0 0;">Tindakan ini tidak bisa dibatalkan</p>
            </div>

            <div style="padding:20px 24px 4px;background:#fff;">
                <p style="font-size:13.5px;color:var(--text-secondary);text-align:center;margin-bottom:12px;">
                    Kamu akan menghapus jurnal berikut:
                </p>
                <div style="background:var(--bg);border:1px solid var(--border);border-radius:var(--r);padding:14px 16px;margin-bottom:8px;">
                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:3px;text-transform:uppercase;letter-spacing:0.05em;">Mata Pelajaran / Kelas</div>
                    <div id="modal-hapus-mapel" style="font-size:15px;font-weight:700;color:var(--text-primary);"></div>
                    <div id="modal-hapus-tanggal" style="font-size:12.5px;color:var(--text-muted);margin-top:5px;">
                        <i class="bi bi-calendar3 me-1"></i>
                    </div>
                </div>
            </div>

            <div style="padding:16px 24px 24px;background:#fff;display:flex;gap:10px;">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Batal
                </button>
                <form id="form-hapus-index" method="POST" class="flex-fill">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash3-fill"></i> Ya, Hapus
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
        const id     = this.dataset.id;
        const mapel  = this.dataset.mapel;
        const kelas  = this.dataset.kelas;
        const tgl    = this.dataset.tanggal;

        document.getElementById('modal-hapus-mapel').textContent   = mapel + ' — ' + kelas;
        document.getElementById('modal-hapus-tanggal').innerHTML    = '<i class="bi bi-calendar3 me-1"></i>' + tgl;
        document.getElementById('form-hapus-index').action = '/guru/jurnal/' + id;

        new bootstrap.Modal(document.getElementById('modalHapusIndex')).show();
    });
});
</script>
@endpush

@endsection