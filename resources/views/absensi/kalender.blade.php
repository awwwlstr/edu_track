@extends('layouts.app')

@section('title', 'Kalender Absensi')

@section('content')
@php
    use Carbon\Carbon;

    $tanggalAwal = Carbon::parse($bulan . '-01');
    $jumlahHari  = $tanggalAwal->daysInMonth;
    $hariPertama = $tanggalAwal->dayOfWeek;
    // $hariBesar sudah dikirim dari controller
@endphp

<style>
    .calendar-header,
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }
    .calendar-header div {
        text-align: center;
        font-weight: bold;
        padding: 10px 0;
        border-bottom: 2px solid #ddd;
    }
    .calendar-header div:first-child { color: #dc3545; }
    .calendar-header div:last-child  { color: #6c757d; }
    .calendar-cell {
        border: 1px solid #eaeaea;
        min-height: 90px;
        padding: 8px;
        border-radius: 6px;
        background: #f8f9fa;
        text-align: center;
    }
    .calendar-cell .tanggal {
        font-weight: bold;
        margin-bottom: 4px;
    }
    .calendar-cell.hadir   { background: #198754; color: #fff; }
    .calendar-cell.izin    { background: #ffc107; color: #000; }
    .calendar-cell.alpha   { background: #dc3545; color: #fff; }
    .calendar-cell.weekend { background: #dee2e6; color: #555; }
    .calendar-cell.libur   { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
    .calendar-cell.libur .tanggal { color: #842029; }
    .libur-label {
        font-size: 10px;
        line-height: 1.2;
        margin-top: 4px;
        font-weight: 600;
    }
    .calendar-cell.empty {
        background: transparent;
        border: none;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-calendar-alt"></i>
            Kalender Absensi - {{ $tanggalAwal->translatedFormat('F Y') }}
        </h2>
        <div>
            <a href="?bulan={{ $tanggalAwal->copy()->subMonth()->format('Y-m') }}" 
               class="btn btn-outline-primary btn-sm">
                <i class="fas fa-chevron-left"></i> Sebelumnya
            </a>
            <a href="?bulan={{ $tanggalAwal->copy()->addMonth()->format('Y-m') }}" 
               class="btn btn-outline-primary btn-sm ms-2">
                Berikutnya <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>

    {{-- Header hari --}}
    <div class="calendar-header">
        <div>Min</div>
        <div>Sen</div>
        <div>Sel</div>
        <div>Rab</div>
        <div>Kam</div>
        <div>Jum</div>
        <div>Sab</div>
    </div>

    {{-- Kalender --}}
    <div class="calendar-grid mt-2">
        {{-- Kosong sebelum tanggal 1 --}}
        @for ($i = 0; $i < $hariPertama; $i++)
            <div class="calendar-cell empty"></div>
        @endfor

        {{-- Tanggal --}}
        @for ($tgl = 1; $tgl <= $jumlahHari; $tgl++)
            @php
                $tanggalFull  = sprintf('%s-%02d', $bulan, $tgl);
                $tanggalObj   = Carbon::parse($tanggalFull);
                $dayOfWeek    = $tanggalObj->dayOfWeek;
                $keyHariBesar = sprintf('%02d', $tgl);

                $data      = $absensi[$tanggalFull] ?? null;
                $isWeekend = in_array($dayOfWeek, [0, 6]);
                $isLibur   = isset($hariBesar[$keyHariBesar]);
                $namaLibur = $isLibur ? $hariBesar[$keyHariBesar] : '';

                // Prioritas: absensi > libur > weekend
                $kelas = '';
                if ($data) {
                    if ($data->status === 'hadir' || $data->status === 'terlambat') {
                        $kelas = 'hadir';
                    } elseif (in_array($data->status, ['izin', 'sakit'])) {
                        $kelas = 'izin';
                    } else {
                        $kelas = 'alpha';
                    }
                } elseif ($isLibur) {
                    $kelas = 'libur';
                } elseif ($isWeekend) {
                    $kelas = 'weekend';
                }
            @endphp

            <div class="calendar-cell {{ $kelas }}">
                <div class="tanggal">{{ $tgl }}</div>
                @if($data)
                    <small>{{ ucfirst($data->status) }}</small>
                @elseif($isLibur)
                    <div class="libur-label">🎉 {{ $namaLibur }}</div>
                @elseif($isWeekend)
                    <small>{{ $dayOfWeek == 0 ? 'Minggu' : 'Sabtu' }}</small>
                @endif
            </div>
        @endfor
    </div>

    {{-- Keterangan warna --}}
    <div class="mt-4 d-flex gap-2 flex-wrap">
        <span class="badge bg-success">Hadir / Terlambat</span>
        <span class="badge bg-warning text-dark">Izin / Sakit</span>
        <span class="badge bg-danger">Alpha</span>
        <span class="badge bg-secondary">Sabtu / Minggu</span>
        <span class="badge" style="background:#f8d7da; color:#842029; border:1px solid #f5c2c7">
            Hari Besar
        </span>
    </div>
</div>
@endsection