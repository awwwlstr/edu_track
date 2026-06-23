@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <div class="page-title">
                <i class="fas fa-history me-2 text-green"></i>Rekap Absensi
            </div>
            <div class="page-subtitle">Riwayat kehadiran bulanan</div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-body" style="padding: 14px 20px;">
            <form action="/absensi/rekap" method="GET" class="d-flex gap-2 align-items-center">
                <i class="fas fa-filter text-green"></i>
                <input type="month" name="bulan" class="form-control" 
                       style="max-width: 200px;" value="{{ $bulan }}">
                <button type="submit" class="btn btn-primary btn-sm">
                    Tampilkan
                </button>
            </form>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card hadir">
                <div class="stat-label">
                    <i class="fas fa-check-circle me-1"></i>Hadir
                </div>
                <div class="stat-value" style="color: var(--color-hadir);">
                    {{ $total['hadir'] }}
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card" style="border-left-color: var(--color-izin);">
                <div class="stat-label">
                    <i class="fas fa-clock me-1"></i>Terlambat
                </div>
                <div class="stat-value" style="color: var(--color-izin);">
                    {{ $total['terlambat'] }}
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card izin">
                <div class="stat-label">
                    <i class="fas fa-file-alt me-1"></i>Izin
                </div>
                <div class="stat-value" style="color: var(--color-sakit);">
                    {{ $total['izin'] }}
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card sakit">
                <div class="stat-label">
                    <i class="fas fa-heartbeat me-1"></i>Sakit
                </div>
                <div class="stat-value" style="color: var(--color-sakit);">
                    {{ $total['sakit'] }}
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Rekap --}}
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-table text-green"></i>
            <span>Riwayat Absensi</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-app mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Hari</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensi as $key => $item)
                        <tr>
                            <td style="color: var(--gray-400); font-size: 12px;">
                                {{ $key + 1 }}
                            </td>
                            <td style="font-family: 'DM Mono', monospace; font-size: 13px;">
                                {{ date('d/m/Y', strtotime($item->tanggal)) }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                            <td style="font-family: 'DM Mono', monospace; font-size: 13px;">
                                {{ $item->jam_masuk ?? '-' }}
                            </td>
                            <td style="font-family: 'DM Mono', monospace; font-size: 13px;">
                                {{ $item->jam_keluar ?? '-' }}
                            </td>
                            <td>
                                @if($item->status == 'hadir')
                                    <span class="badge-status badge-hadir">
                                        <i class="fas fa-check-circle" style="font-size: 9px;"></i> Hadir
                                    </span>
                                @elseif($item->status == 'terlambat')
                                    <span class="badge-status badge-izin">
                                        <i class="fas fa-clock" style="font-size: 9px;"></i> Terlambat
                                    </span>
                                @elseif($item->status == 'izin')
                                    <span class="badge-status badge-sakit">
                                        <i class="fas fa-file-alt" style="font-size: 9px;"></i> Izin
                                    </span>
                                @elseif($item->status == 'sakit')
                                    <span class="badge-status" 
                                          style="background: #e0f2fe; color: #075985;">
                                        <i class="fas fa-heartbeat" style="font-size: 9px;"></i> Sakit
                                    </span>
                                @else
                                    <span class="badge-status badge-alpha">
                                        <i class="fas fa-times-circle" style="font-size: 9px;"></i> Alpha
                                    </span>
                                @endif
                            </td>
                            <td style="color: var(--gray-500); font-size: 13px;">
                                {{ $item->keterangan ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center" 
                                style="padding: 40px; color: var(--gray-400);">
                                <i class="fas fa-inbox" style="font-size: 28px; display: block; margin-bottom: 8px; opacity: 0.4;"></i>
                                Tidak ada data absensi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection