{{-- FILE: kepsek/guru/index.blade.php --}}
@extends('layouts.kepsek')

@section('title', 'Data Guru')

@section('content')

<div class="p-4 fade-up">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('kepsek.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="page-header" style="margin:0;">
            <h4 style="margin:0;">Data Guru</h4>
            <small>Daftar seluruh guru yang terdaftar di sistem</small>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-people me-2 text-success"></i>Daftar Guru</span>
            <span class="badge bg-primary">Total: {{ $guru->total() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>NIP</th>
                        <th>Jabatan</th>
                        <th style="width:100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($guru as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if(optional($item->guru)->foto)
                                    <img src="{{ asset('storage/'.$item->guru->foto) }}"
                                         class="rounded-circle"
                                         style="width:34px;height:34px;object-fit:cover;"
                                         alt="Foto">
                                @else
                                    <div style="width:34px;height:34px;background:var(--brand-light);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--brand);flex-shrink:0;">
                                        {{ strtoupper(substr($item->nama_lengkap, 0, 1)) }}
                                    </div>
                                @endif
                                <span style="font-weight:600;">{{ $item->nama_lengkap }}</span>
                            </div>
                        </td>
                        <td style="color:var(--text-muted);">{{ $item->email }}</td>
                        <td>{{ optional($item->guru)->nip ?? '—' }}</td>
                        <td>{{ optional($item->guru)->jabatan ?? '—' }}</td>
                        <td class="text-center">
                            <a href="{{ route('kepsek.guru.show', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                            Belum ada data guru
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($guru->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $guru->links() }}
        </div>
        @endif
    </div>

</div>

@endsection