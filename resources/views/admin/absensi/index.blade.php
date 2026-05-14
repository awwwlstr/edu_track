@extends('layouts.admin')

@section('title', 'Data Absensi')

@section('content')

{{-- Alert --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Page Header --}}
<div class="d-flex align-items-center gap-2 mb-4">
    <div style="width:38px;height:38px;border-radius:var(--radius-md);background:var(--jade-soft);color:var(--jade-dark);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fas fa-clipboard-list"></i>
    </div>
    <div>
        <div class="fw-bold" style="font-size:1rem;color:var(--text);">Data Absensi</div>
        <small style="color:var(--text-muted);">Rekap kehadiran semua guru</small>
    </div>
</div>

{{-- STATISTIK RINGKASAN --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon green">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="info">
                <p>Hadir Hari Ini</p>
                <h4>{{ $hadirHariIni ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon yellow">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="info">
                <p>Izin Hari Ini</p>
                <h4>{{ $izinHariIni ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon red">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="info">
                <p>Alpha Hari Ini</p>
                <h4>{{ $alphaHariIni ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="info">
                <p>Total Data</p>
                <h4>{{ count($absensi) }}</h4>
            </div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-sliders-h me-2 text-jade"></i>Filter Data
    </div>
    <div class="card-body">
        <form action="/admin/absensi" method="GET">
            <div class="row g-3">

                <div class="col-12 col-md-4">
                    <label class="form-label">Bulan</label>
                    <input type="month" name="bulan" class="form-control" value="{{ $bulan }}">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Guru</label>
                    <select name="guru_id" class="form-select">
                        <option value="">— Semua Guru —</option>
                        @foreach($guru as $item)
                            <option value="{{ $item->id_user }}"
                                {{ $guruId == $item->id_user ? 'selected' : '' }}>
                                {{ $item->nama }} ({{ $item->nip }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label d-none d-md-block">&nbsp;</label>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="/admin/absensi"
                           style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-md);border:1.5px solid var(--line);background:var(--surface-2);color:var(--text-muted);font-size:0.875rem;font-weight:500;text-decoration:none;transition:all var(--transition);">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                        <a href="/admin/absensi/export-pdf?bulan={{ $bulan }}"
                           style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-md);border:1px solid #fecaca;background:#fee2e2;color:#991b1b;font-size:0.875rem;font-weight:500;text-decoration:none;transition:all var(--transition);">
                            <i class="fas fa-file-pdf"></i> Semua Guru
                        </a>
                        @if($guruId)
                        <a href="/admin/absensi/export-pdf?bulan={{ $bulan }}&guru_id={{ $guruId }}"
                           style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-md);border:1px solid #fde68a;background:#fef3c7;color:#92400e;font-size:0.875rem;font-weight:500;text-decoration:none;transition:all var(--transition);">
                            <i class="fas fa-file-pdf"></i> Guru Ini
                        </a>
                        @endif
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span>
            <i class="fas fa-table me-2 text-jade"></i>Daftar Absensi
        </span>
        <span class="font-mono"
              style="font-size:0.75rem;color:var(--text-light);background:var(--surface-2);border:1px solid var(--line);padding:2px 10px;border-radius:20px;">
            {{ count($absensi) }} data
        </span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Guru</th>
                    <th>NIP</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensi as $key => $item)
                <tr>
                    <td>
                        <span class="font-mono" style="color:var(--text-light);font-size:0.78rem;">
                            {{ $key + 1 }}
                        </span>
                    </td>
                    <td>
                        <span class="font-mono" style="font-size:0.82rem;">
                            {{ date('d/m/Y', strtotime($item->tanggal)) }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight:600;font-size:0.875rem;">
                            {{ $item->user->nama }}
                        </span>
                    </td>
                    <td>
                        <span class="font-mono" style="font-size:0.78rem;color:var(--text-muted);">
                            {{ $item->user->nip }}
                        </span>
                    </td>
                    <td>
                        <span class="font-mono" style="font-size:0.82rem;">
                            {{ $item->jam_masuk ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <span class="font-mono" style="font-size:0.82rem;">
                            {{ $item->jam_keluar ?? '—' }}
                        </span>
                    </td>
                    <td>
                        @if($item->status == 'hadir')
                            <span class="badge" style="background:var(--jade-soft);color:var(--jade-dark);">
                                <i class="fas fa-check me-1"></i>Hadir
                            </span>
                        @elseif($item->status == 'terlambat')
                            <span class="badge" style="background:#fef3c7;color:#92400e;">
                                <i class="fas fa-clock me-1"></i>Terlambat
                            </span>
                        @elseif($item->status == 'izin')
                            <span class="badge" style="background:#dbeafe;color:#1e40af;">
                                <i class="fas fa-envelope me-1"></i>Izin
                            </span>
                        @elseif($item->status == 'sakit')
                            <span class="badge" style="background:#f1f5f9;color:var(--text-muted);">
                                <i class="fas fa-notes-medical me-1"></i>Sakit
                            </span>
                        @elseif($item->status == 'alpha')
                            <span class="badge" style="background:#fee2e2;color:#991b1b;">
                                <i class="fas fa-times me-1"></i>Alpha
                            </span>
                        @else
                            <span class="badge" style="background:var(--surface-2);color:var(--text-muted);">
                                {{ $item->status }}
                            </span>
                        @endif
                    </td>
                    <td style="max-width:160px;">
                        <span style="font-size:0.82rem;color:var(--text-muted);display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $item->keterangan ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <form action="/admin/absensi/{{ $item->id_absensi }}" method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    title="Hapus"
                                    style="width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid #fecaca;background:#fee2e2;color:#991b1b;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all var(--transition);">
                                <i class="fas fa-trash-alt" style="font-size:0.75rem;pointer-events:none;"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:48px 20px;">
                        <i class="fas fa-inbox" style="font-size:2rem;color:var(--text-light);display:block;margin-bottom:10px;"></i>
                        <span style="color:var(--text-muted);font-size:0.875rem;">
                            Tidak ada data absensi untuk filter ini.
                        </span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection