@extends('layouts.kepsek')

@section('title', 'Jurnal Guru')

@section('content')

<div class="p-4 fade-up">

    {{-- HEADER --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('kepsek.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="page-header" style="margin:0;">
            <h4 style="margin:0;">Jurnal Mengajar Guru</h4>
            <small>Daftar seluruh jurnal yang telah diinput guru</small>
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
                        <th>Status Evaluasi</th>
                        <th style="width:100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($jurnal as $item)
                    <tr>
                        <td>
                            <div style="font-weight:600;font-size:13.5px;">
                                {{ optional(optional($item->guru)->user)->nama_lengkap ?? '-' }}
                            </div>
                        </td>
                        <td>{{ $item->tanggal->format('d M Y') }}</td>
                        <td style="font-weight:500;">{{ $item->mata_pelajaran }}</td>
                        <td>{{ $item->kelas }}</td>
                        <td>
                            @if($item->evaluasi)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Sudah Dievaluasi</span>
                            @else
                                <span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Belum Dievaluasi</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('kepsek.jurnal.show', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                            Belum ada jurnal mengajar
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($jurnal->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $jurnal->links() }}
        </div>
        @endif

    </div>

</div>

@endsection