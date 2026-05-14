@extends('layouts.admin')

@section('title', 'Detail Jurnal')

@section('content')
<div class="p-4 fade-up">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.evaluasi.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="page-header" style="margin:0;">
            <h4 style="margin:0;">Detail Jurnal</h4>
            <small>{{ $jurnal->mata_pelajaran }} — {{ $jurnal->kelas }}</small>
        </div>
    </div>

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

    <div class="row g-4">

        {{-- DETAIL JURNAL --}}
        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-journal-text me-2 text-success"></i>Isi Jurnal
                </div>
                <div class="card-body">
                    <table class="table table-borderless" style="font-size:14px;">
                        <tr>
                            <td style="width:140px;color:var(--text-muted);">Guru</td>
                            <td><strong>{{ optional($jurnal->user)->nama ?? '—' }}</strong></td>
                        </tr>
                        <tr>
                            <td style="color:var(--text-muted);">Tanggal</td>
                            <td>{{ $jurnal->tanggal->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td style="color:var(--text-muted);">Jam Pelajaran</td>
                            <td>{{ $jurnal->jam_pelajaran }}</td>
                        </tr>
                        <tr>
                            <td style="color:var(--text-muted);">Mata Pelajaran</td>
                            <td>{{ $jurnal->mata_pelajaran }}</td>
                        </tr>
                        <tr>
                            <td style="color:var(--text-muted);">Kelas</td>
                            <td>{{ $jurnal->kelas }}</td>
                        </tr>
                        <tr>
                            <td style="color:var(--text-muted);">Status</td>
                            <td>
                                @if($jurnal->status === 'dinilai')
                                    <span class="badge bg-success">Sudah Dievaluasi</span>
                                @elseif($jurnal->status === 'revisi')
                                    <span class="badge bg-danger">Revisi</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--text-muted);vertical-align:top;">Materi</td>
                            <td>{{ $jurnal->materi }}</td>
                        </tr>
                        @if($jurnal->kendala)
                        <tr>
                            <td style="color:var(--text-muted);vertical-align:top;">Kendala</td>
                            <td>{{ $jurnal->kendala }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- FORM EVALUASI --}}
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-clipboard-check me-2 text-success"></i>
                    {{ $jurnal->evaluasi ? 'Hasil Evaluasi' : 'Beri Evaluasi' }}
                </div>
                <div class="card-body">
                    @if($jurnal->evaluasi)
                        {{-- Sudah dievaluasi, tampilkan hasil --}}
                        <div class="text-center mb-3">
                            @php
                                $nilai = $jurnal->evaluasi->nilai;
                                $nc = $nilai >= 85 ? 'bg-success' : ($nilai >= 70 ? 'bg-primary' : ($nilai >= 60 ? 'bg-warning' : 'bg-danger'));
                            @endphp
                            <div style="font-size:48px;font-weight:800;" class="text-success">{{ $nilai }}</div>
                            <span class="badge {{ $nc }} fs-6">
                                {{ $nilai >= 85 ? 'Sangat Baik' : ($nilai >= 70 ? 'Baik' : ($nilai >= 60 ? 'Cukup' : 'Kurang')) }}
                            </span>
                        </div>
                        @if($jurnal->evaluasi->catatan)
                            <div class="alert alert-light" style="font-size:13.5px;">
                                <strong>Catatan:</strong><br>{{ $jurnal->evaluasi->catatan }}
                            </div>
                        @endif
                        <p class="text-muted text-center" style="font-size:12px;">
                            <i class="bi bi-lock-fill me-1"></i>Jurnal sudah dievaluasi dan tidak bisa diubah
                        </p>
                    @else
                        {{-- Belum dievaluasi, tampilkan form --}}
                        <form method="POST" action="{{ route('admin.evaluasi.store', $jurnal->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nilai <span class="text-danger">*</span></label>
                                <input type="number" name="nilai" class="form-control"
                                       min="1" max="100" placeholder="1 - 100"
                                       value="{{ old('nilai') }}" required>
                                @error('nilai')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Catatan</label>
                                <textarea name="catatan" class="form-control" rows="4"
                                          placeholder="Catatan untuk guru (opsional)">{{ old('catatan') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-send-fill me-1"></i> Simpan Evaluasi
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection