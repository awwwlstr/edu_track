@extends('layouts.admin')

@section('title', 'Data Evaluasi Jurnal')

@section('content')
<div class="p-4 fade-up">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="/admin" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="page-header" style="margin:0;">
            <h4 style="margin:0;">Evaluasi Jurnal Guru</h4>
            <small>Daftar seluruh jurnal yang perlu dievaluasi</small>
        </div>
    </div>

    {{-- FILTER --}}
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
                        <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Belum Dievaluasi</option>
                        <option value="dinilai"  {{ request('status') === 'dinilai'  ? 'selected' : '' }}>Sudah Dievaluasi</option>
                        <option value="revisi"   {{ request('status') === 'revisi'   ? 'selected' : '' }}>Revisi</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-journal-text me-2 text-success"></i>Daftar Jurnal</span>
            <span class="badge bg-primary">Total: {{ $jurnal->total() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Guru</th>
                        <th>Tanggal</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th class="text-center" style="width:100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($jurnal as $item)
                    <tr>
                        <td style="font-weight:600;">{{ optional($item->user)->nama ?? '—' }}</td>
                        <td>{{ $item->tanggal->format('d M Y') }}</td>
                        <td>{{ $item->mata_pelajaran }}</td>
                        <td>{{ $item->kelas }}</td>
                        <td>
                            @if($item->status === 'dinilai')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Sudah Dievaluasi</span>
                            @elseif($item->status === 'revisi')
                                <span class="badge bg-danger">Revisi</span>
                            @else
                                <span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Pending</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.evaluasi.show', $item->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
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
@endsection