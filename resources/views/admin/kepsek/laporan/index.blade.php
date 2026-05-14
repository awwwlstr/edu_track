@extends('layouts.kepsek')

@section('title', 'Laporan')

@section('content')

<div class="p-4 fade-up">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('kepsek.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="page-header" style="margin:0;">
            <h4 style="margin:0;">Laporan Sistem Monitoring</h4>
            <small>Ringkasan aktivitas jurnal dan evaluasi guru</small>
        </div>
    </div>

    {{-- FILTER PERIODE --}}
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-funnel me-2 text-success"></i>Filter Periode
        </div>
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        @foreach(range(1,12) as $b)
                            <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        @foreach(range(now()->year, now()->year - 4) as $t)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('kepsek.laporan.index') }}" class="btn btn-secondary w-100">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- STATISTIK KESELURUHAN --}}
    <div style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:12px;">
        Statistik Keseluruhan
    </div>
    <div class="row g-3 mb-4 stagger">

        <div class="col-sm-6 col-md-3">
            <div class="stat-box">
                <div style="width:40px;height:40px;background:#dbeafe;border-radius:var(--r);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                    <i class="bi bi-people-fill" style="font-size:17px;color:#1d4ed8;"></i>
                </div>
                <div class="stat-title">Total Guru</div>
                <div class="stat-value" style="color:#1d4ed8;">{{ $totalGuru }}</div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="stat-box">
                <div style="width:40px;height:40px;background:var(--info-light);border-radius:var(--r);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                    <i class="bi bi-journal-text" style="font-size:17px;color:var(--info);"></i>
                </div>
                <div class="stat-title">Total Jurnal</div>
                <div class="stat-value" style="color:var(--info);">{{ $totalJurnal }}</div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="stat-box">
                <div style="width:40px;height:40px;background:var(--brand-light);border-radius:var(--r);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                    <i class="bi bi-clipboard-check-fill" style="font-size:17px;color:var(--brand);"></i>
                </div>
                <div class="stat-title">Total Evaluasi</div>
                <div class="stat-value">{{ $totalEvaluasi }}</div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="stat-box">
                <div style="width:40px;height:40px;background:var(--warning-light);border-radius:var(--r);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                    <i class="bi bi-hourglass-split" style="font-size:17px;color:var(--warning);"></i>
                </div>
                <div class="stat-title">Belum Dievaluasi</div>
                <div class="stat-value" style="color:var(--warning);">{{ $belumEvaluasi }}</div>
            </div>
        </div>

    </div>

    {{-- STATISTIK PERIODE --}}
    <div style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:12px;">
        Periode: {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
    </div>
    <div class="row g-3">

        <div class="col-md-4">
            <div class="stat-box">
                <div style="width:40px;height:40px;background:#dbeafe;border-radius:var(--r);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                    <i class="bi bi-calendar-check" style="font-size:17px;color:#1d4ed8;"></i>
                </div>
                <div class="stat-title">Jurnal Periode Ini</div>
                <div class="stat-value" style="color:#1d4ed8;">{{ $jurnalPeriode }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-box">
                <div style="width:40px;height:40px;background:var(--brand-light);border-radius:var(--r);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                    <i class="bi bi-clipboard-check" style="font-size:17px;color:var(--brand);"></i>
                </div>
                <div class="stat-title">Evaluasi Periode Ini</div>
                <div class="stat-value">{{ $evaluasiPeriode }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-box">
                <div style="width:40px;height:40px;background:var(--warning-light);border-radius:var(--r);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                    <i class="bi bi-star-fill" style="font-size:17px;color:var(--warning);"></i>
                </div>
                <div class="stat-title">Rata-rata Nilai</div>
                <div class="stat-value" style="color:var(--warning);">
                    {{ $rataRataPeriode ? number_format($rataRataPeriode, 1) : '—' }}
                </div>
            </div>
        </div>

    </div>

</div>

@endsection