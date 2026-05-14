@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-history"></i> Rekap Absensi</h2>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-3">
        <div class="col-md-4">
            <form action="/absensi/rekap" method="GET" class="d-flex gap-2">
                <input type="month" name="bulan" class="form-control" value="{{ $bulan }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-custom stat-card hadir">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $total['hadir'] }}</h3>
                    <small>Hadir</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-custom stat-card hadir">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $total['terlambat'] }}</h3>
                    <small>Terlambat</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-custom stat-card izin">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $total['izin'] }}</h3>
                    <small>Izin</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-custom stat-card sakit">
                <div class="card-body text-center">
                    <h3 class="text-secondary">{{ $total['sakit'] }}</h3>
                    <small>Sakit</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Rekap -->
    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Riwayat Absensi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
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
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                                    <td>{{ $item->jam_masuk ?? '-' }}</td>
                                    <td>{{ $item->jam_keluar ?? '-' }}</td>
                                    <td>
                                        @if($item->status == 'hadir')
                                            <span class="badge bg-success">Hadir</span>
                                        @elseif($item->status == 'terlambat')
                                            <span class="badge bg-warning">Terlambat</span>
                                        @elseif($item->status == 'izin')
                                            <span class="badge bg-info">Izin</span>
                                        @elseif($item->status == 'sakit')
                                            <span class="badge bg-secondary">Sakit</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Tidak ada data absensi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection