@extends('layouts.app')

@section('title', 'Detail Jurnal')

@section('content')

<div class="p-4 fade-up">

    {{-- HEADER --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('jurnal.index') }}" class="btn btn-secondary btn-sm">
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

        {{-- KIRI --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-journal-text me-2 text-success"></i>Informasi Jurnal</span>
                    @php
                        $statusMap = [
                            'pending' => ['bg-warning', 'bi-clock',                 'Pending'],
                            'dinilai' => ['bg-success', 'bi-check-circle-fill',      'Sudah Dinilai'],
                            'revisi'  => ['bg-danger',  'bi-arrow-counterclockwise', 'Perlu Revisi'],
                        ];
                        [$badgeClass, $icon, $label] = $statusMap[$jurnal->status] ?? ['bg-secondary','bi-question-circle', ucfirst($jurnal->status)];
                    @endphp
                    <span class="badge {{ $badgeClass }}">
                        <i class="bi {{ $icon }}"></i> {{ $label }}
                    </span>
                </div>
                <div class="card-body">

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-calendar3 text-success"></i>
                                    <span class="form-label mb-0">Tanggal</span>
                                </div>
                                <div style="font-size:15px;font-weight:600;">
                                    {{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d F Y') }}
                                </div>
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
                            <i class="bi bi-file-earmark-text text-success"></i>
                            Materi Pembelajaran
                        </div>
                        <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);line-height:1.75;white-space:pre-wrap;">{{ $jurnal->materi }}</div>
                    </div>

                    <div>
                        <div class="form-label d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            Kendala / Catatan
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

        {{-- KANAN --}}
        <div class="col-lg-4">

            {{-- EVALUASI --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-star-fill me-2 text-warning"></i>Evaluasi Kepala Sekolah
                </div>
                <div class="card-body text-center">
                    @if($jurnal->status === 'dinilai' && $jurnal->evaluasi)
                        <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px;">Nilai</div>
                        @php
                            $nilai = $jurnal->evaluasi->nilai;
                            $nc = $nilai >= 85 ? 'bg-success' : ($nilai >= 70 ? 'bg-primary' : ($nilai >= 55 ? 'bg-warning' : 'bg-danger'));
                        @endphp
                        <span class="badge fs-6 {{ $nc }} mb-3">{{ $nilai }}</span>
                        @if($jurnal->evaluasi->catatan)
                            <div class="text-start">
                                <div class="form-label">Catatan Kepala Sekolah</div>
                                <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);font-size:13px;line-height:1.7;white-space:pre-wrap;">{{ $jurnal->evaluasi->catatan }}</div>
                            </div>
                        @endif
                        <div style="font-size:11.5px;color:var(--text-muted);margin-top:12px;">
                            Dinilai pada {{ \Carbon\Carbon::parse($jurnal->evaluasi->created_at)->translatedFormat('d F Y') }}
                        </div>
                    @elseif($jurnal->status === 'revisi')
                        <div class="py-2">
                            <i class="bi bi-arrow-counterclockwise text-danger" style="font-size:32px;"></i>
                            <p class="mt-2 mb-0" style="font-size:13px;color:var(--danger-text);">Jurnal ini perlu direvisi.<br>Silakan edit dan kirim ulang.</p>
                        </div>
                    @else
                        <div class="py-2">
                            <i class="bi bi-hourglass-split text-muted" style="font-size:32px;"></i>
                            <p class="mt-2 mb-0 text-muted" style="font-size:13px;">Belum dinilai.<br>Menunggu evaluasi kepala sekolah.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- AKSI --}}
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-lightning-fill me-2 text-success"></i>Aksi
                </div>
                <div class="card-body d-grid gap-2">
                    @if($jurnal->status !== 'dinilai')
                        <a href="{{ route('jurnal.edit', $jurnal->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square"></i> Edit Jurnal
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus">
                            <i class="bi bi-trash3"></i> Hapus Jurnal
                        </button>
                    @else
                        <button class="btn btn-secondary" disabled>
                            <i class="bi bi-lock-fill"></i> Tidak Bisa Diedit
                        </button>
                    @endif
                    <a href="{{ route('jurnal.index') }}" class="btn btn-secondary">
                        <i class="bi bi-list-ul"></i> Semua Jurnal
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>


{{-- MODAL HAPUS --}}
@if($jurnal->status !== 'dinilai')
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content" style="border:none;border-radius:var(--r-xl);overflow:hidden;box-shadow:var(--shadow-lg);">

            <div style="background:var(--danger);padding:28px 24px 22px;text-align:center;">
                <div style="width:60px;height:60px;background:rgba(255,255,255,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <i class="bi bi-trash3-fill" style="font-size:26px;color:#fff;"></i>
                </div>
                <h5 style="color:#fff;font-weight:800;font-size:18px;margin:0;">Hapus Jurnal?</h5>
                <p style="color:rgba(255,255,255,0.75);font-size:13px;margin:6px 0 0;">Tindakan ini tidak bisa dibatalkan</p>
            </div>

            <div style="padding:20px 24px 4px;background:#fff;">
                <p style="font-size:13.5px;color:var(--text-secondary);text-align:center;margin-bottom:12px;">Kamu akan menghapus jurnal berikut:</p>
                <div style="background:var(--bg);border:1px solid var(--border);border-radius:var(--r);padding:14px 16px;">
                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:3px;text-transform:uppercase;letter-spacing:0.05em;">Mata Pelajaran / Kelas</div>
                    <div style="font-size:15px;font-weight:700;color:var(--text-primary);">{{ $jurnal->mata_pelajaran }} — {{ $jurnal->kelas }}</div>
                    <div style="font-size:12.5px;color:var(--text-muted);margin-top:5px;">
                        <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d F Y') }}
                    </div>
                </div>
            </div>

            <div style="padding:16px 24px 24px;background:#fff;display:flex;gap:10px;">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Batal
                </button>
                <form action="{{ route('jurnal.destroy', $jurnal->id) }}" method="POST" class="flex-fill">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash3-fill"></i> Ya, Hapus
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endif

@endsection