@extends('layouts.kepsek')

@section('title', 'Detail Jurnal')

@section('content')

<div class="p-4 fade-up">

    {{-- HEADER --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('kepsek.jurnal.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="page-header" style="margin:0;">
            <h4 style="margin:0;">Detail Jurnal</h4>
            <small>{{ $jurnal->mata_pelajaran }} — {{ $jurnal->kelas }}</small>
        </div>
    </div>

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

    <div class="row g-4">

        {{-- KIRI: INFO JURNAL --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-journal-text me-2 text-success"></i>Informasi Jurnal</span>
                    @php
                        $statusMap = [
                            'pending' => ['bg-warning', 'bi-clock',                 'Pending'],
                            'dinilai' => ['bg-success', 'bi-check-circle-fill',      'Sudah Dinilai'],
                            'revisi'  => ['bg-danger',  'bi-arrow-counterclockwise', 'Perlu Revisi'],
                        ];
                        [$badgeClass, $icon, $label] = $statusMap[$jurnal->status] ?? ['bg-secondary','bi-question-circle',ucfirst($jurnal->status)];
                    @endphp
                    <span class="badge {{ $badgeClass }}">
                        <i class="bi {{ $icon }}"></i> {{ $label }}
                    </span>
                </div>
                <div class="card-body">

                    {{-- INFO GURU --}}
                    <div class="d-flex align-items-center gap-3 p-3 mb-4 rounded-3" style="background:var(--bg);border:1px solid var(--border);">
                        <div style="width:44px;height:44px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#1d4ed8;flex-shrink:0;">
                            {{ strtoupper(substr(optional(optional($jurnal->guru)->user)->nama_lengkap ?? 'G', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:14px;font-weight:700;color:var(--text-primary);">
                                {{ optional(optional($jurnal->guru)->user)->nama_lengkap ?? '-' }}
                            </div>
                            <div style="font-size:12px;color:var(--text-muted);">Guru</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-calendar3 text-success"></i>
                                    <span class="form-label mb-0">Tanggal</span>
                                </div>
                                <div style="font-size:15px;font-weight:600;">{{ $jurnal->tanggal->translatedFormat('d F Y') }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-clock text-success"></i>
                                    <span class="form-label mb-0">Jam Pelajaran</span>
                                </div>
                                <div style="font-size:15px;font-weight:600;">{{ $jurnal->jam_pelajaran }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-book text-success"></i>
                                    <span class="form-label mb-0">Mata Pelajaran</span>
                                </div>
                                <div style="font-size:15px;font-weight:600;">{{ $jurnal->mata_pelajaran }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-people text-success"></i>
                                    <span class="form-label mb-0">Kelas</span>
                                </div>
                                <div style="font-size:15px;font-weight:600;">{{ $jurnal->kelas }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-label d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-text text-success"></i>Materi Pembelajaran
                        </div>
                        <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);line-height:1.75;white-space:pre-wrap;">{{ $jurnal->materi }}</div>
                    </div>

                    <div>
                        <div class="form-label d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle text-warning"></i>Kendala / Catatan
                        </div>
                        @if($jurnal->kendala)
                            <div class="p-3 rounded-3" style="background:var(--warning-light);border:1px solid #fde68a;color:var(--warning-text);line-height:1.75;white-space:pre-wrap;">{{ $jurnal->kendala }}</div>
                        @else
                            <div class="p-3 rounded-3 text-muted fst-italic" style="background:var(--bg);border:1px solid var(--border);">Tidak ada kendala</div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        {{-- KANAN: EVALUASI --}}
        <div class="col-lg-5">

            @if($jurnal->evaluasi)

                {{-- SUDAH DIEVALUASI --}}
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-check-circle-fill me-2 text-success"></i>Hasil Evaluasi
                    </div>
                    <div class="card-body text-center">
                        <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px;">Nilai</div>
                        @php
                            $nilai = $jurnal->evaluasi->nilai;
                            $nc = $nilai >= 85 ? 'bg-success' : ($nilai >= 70 ? 'bg-primary' : ($nilai >= 60 ? 'bg-warning' : 'bg-danger'));
                        @endphp
                        <span class="badge fs-6 {{ $nc }} mb-3">{{ $nilai }}</span>

                        @if($jurnal->evaluasi->catatan)
                            <div class="text-start mb-3">
                                <div class="form-label">Catatan</div>
                                <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);font-size:13px;line-height:1.7;white-space:pre-wrap;">{{ $jurnal->evaluasi->catatan }}</div>
                            </div>
                        @endif

                        <div style="font-size:11.5px;color:var(--text-muted);">
                            Dievaluasi pada {{ $jurnal->evaluasi->created_at->translatedFormat('d F Y') }}
                        </div>
                    </div>
                </div>

            @else

                {{-- FORM EVALUASI --}}
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-clipboard-check me-2 text-success"></i>Beri Evaluasi
                    </div>
                    <div class="card-body">

                        @if($errors->any())
                            <div class="alert alert-danger mb-3">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                <ul class="mb-0 mt-1 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('kepsek.jurnal.evaluasi', $jurnal->id) }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">
                                    Nilai
                                    <span style="font-weight:400;color:var(--text-muted);font-size:12px;">(1–100)</span>
                                </label>
                                <input type="number" name="nilai"
                                       class="form-control @error('nilai') is-invalid @enderror"
                                       min="1" max="100"
                                       value="{{ old('nilai') }}"
                                       placeholder="Masukkan nilai" required>
                                @error('nilai')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    Catatan
                                    <span style="font-weight:400;color:var(--text-muted);font-size:12px;">(Opsional)</span>
                                </label>
                                <textarea name="catatan" rows="4" class="form-control"
                                          placeholder="Tulis catatan untuk guru">{{ old('catatan') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-save"></i> Simpan Evaluasi
                            </button>

                        </form>
                    </div>
                </div>

            @endif

        </div>

    </div>

</div>

@endsection