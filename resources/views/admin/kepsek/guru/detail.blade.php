@extends('layouts.kepsek')

@section('title', 'Detail Guru')

@section('content')

<div class="p-4 fade-up">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('kepsek.guru.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="page-header" style="margin:0;">
            <h4 style="margin:0;">Detail Guru</h4>
            <small>Informasi lengkap data guru</small>
        </div>
    </div>

    <div class="row g-4">

        {{-- KARTU PROFIL --}}
        <div class="col-lg-4">
            <div class="card text-center">
                <div class="card-body p-4">

                    @if(optional($guru->guru)->foto)
                        <img src="{{ asset('storage/'.$guru->guru->foto) }}"
                             class="rounded-circle mb-3"
                             style="width:90px;height:90px;object-fit:cover;border:3px solid var(--border);"
                             alt="Foto Guru">
                    @else
                        <div style="width:90px;height:90px;background:linear-gradient(135deg,var(--brand-light),#a7f3d0);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:32px;font-weight:800;color:var(--brand-deeper);">
                            {{ strtoupper(substr($guru->nama_lengkap, 0, 1)) }}
                        </div>
                    @endif

                    <div style="font-size:17px;font-weight:800;color:var(--text-primary);letter-spacing:-0.3px;">
                        {{ $guru->nama_lengkap }}
                    </div>
                    <div style="font-size:13px;color:var(--text-muted);margin-top:4px;">
                        {{ $guru->email }}
                    </div>

                    @if(optional($guru->guru)->jabatan)
                        <div class="mt-3">
                            <span class="badge bg-success">{{ $guru->guru->jabatan }}</span>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- DATA DETAIL --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-lines-fill me-2 text-success"></i>Data Pribadi
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);">
                                <div style="font-size:11.5px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">NIP</div>
                                <div style="font-weight:600;">{{ optional($guru->guru)->nip ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);">
                                <div style="font-size:11.5px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Jabatan</div>
                                <div style="font-weight:600;">{{ optional($guru->guru)->jabatan ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);">
                                <div style="font-size:11.5px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Tempat Lahir</div>
                                <div style="font-weight:600;">{{ optional($guru->guru)->tempat_lahir ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);">
                                <div style="font-size:11.5px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Tanggal Lahir</div>
                                <div style="font-weight:600;">
                                    @if(optional($guru->guru)->tanggal_lahir)
                                        {{ $guru->guru->tanggal_lahir->translatedFormat('d F Y') }}
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);">
                                <div style="font-size:11.5px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Jenis Kelamin</div>
                                <div style="font-weight:600;">{{ optional($guru->guru)->jenis_kelamin ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 rounded-3" style="background:var(--bg);border:1px solid var(--border);">
                                <div style="font-size:11.5px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Alamat</div>
                                <div style="font-weight:600;">{{ optional($guru->guru)->alamat ?? '—' }}</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection