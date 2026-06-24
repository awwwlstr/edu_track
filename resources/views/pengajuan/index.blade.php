@extends('layouts.app')

@section('title', 'Pengajuan Izin/Sakit')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-file-alt me-2 text-green"></i>Pengajuan Izin/Sakit
        </div>
        <div class="page-subtitle">Ajukan izin atau sakit untuk disetujui admin</div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="app-alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Form Pengajuan --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-plus-circle text-green"></i>
            <span>Ajukan Izin/Sakit Baru</span>
        </div>
        <div class="card-body p-4">
            <form action="/absensi/pengajuan" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">
                            Jenis <span style="color: var(--color-alpha);">*</span>
                        </label>
                        <select name="jenis" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                        @error('jenis')
                            <small style="color: var(--color-alpha);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            Tanggal Mulai <span style="color: var(--color-alpha);">*</span>
                        </label>
                        <input type="date" name="tanggal_mulai" class="form-control" required>
                        @error('tanggal_mulai')
                            <small style="color: var(--color-alpha);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            Tanggal Selesai <span style="color: var(--color-alpha);">*</span>
                        </label>
                        <input type="date" name="tanggal_selesai" class="form-control" required>
                        @error('tanggal_selesai')
                            <small style="color: var(--color-alpha);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">
                            Alasan <span style="color: var(--color-alpha);">*</span>
                        </label>
                        <textarea name="alasan" class="form-control" rows="3" required
                                  placeholder="Jelaskan alasan izin/sakit..."></textarea>
                        @error('alasan')
                            <small style="color: var(--color-alpha);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Surat Keterangan <span style="color:var(--gray-400);font-weight:400;">(Opsional)</span></label>
                        <input type="file" name="surat_keterangan" class="form-control"
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small style="color: var(--gray-400); font-size: 12px; margin-top: 4px; display:block;">
                            <i class="fas fa-paperclip me-1"></i>Format: PDF, JPG, PNG (Maks. 2MB)
                        </small>
                        @error('surat_keterangan')
                            <small style="color: var(--color-alpha); display:block;">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                <div class="mt-4 pt-3" style="border-top: 1px solid var(--gray-100);">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Riwayat Pengajuan --}}
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-history text-green"></i>
            <span>Riwayat Pengajuan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-app mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuan as $key => $item)
                        <tr>
                            <td style="color: var(--gray-400); font-size: 12px;">
                                {{ $key + 1 }}
                            </td>
                            <td>
                                @if($item->jenis == 'izin')
                                    <span class="badge-status badge-sakit">
                                        <i class="fas fa-file-alt" style="font-size:9px;"></i> Izin
                                    </span>
                                @else
                                    <span class="badge-status" style="background: #e0f2fe; color: #075985;">
                                        <i class="fas fa-heartbeat" style="font-size:9px;"></i> Sakit
                                    </span>
                                @endif
                            </td>
                            <td style="font-family: 'DM Mono', monospace; font-size: 12px;">
                                {{ date('d/m/Y', strtotime($item->tanggal_mulai)) }}
                                <span style="color: var(--gray-400);">—</span>
                                {{ date('d/m/Y', strtotime($item->tanggal_selesai)) }}
                            </td>
                            <td style="color: var(--gray-600); font-size: 13px;">
                                {{ Str::limit($item->alasan, 50) }}
                            </td>
                            <td>
                                @if($item->status == 'menunggu')
                                    <span class="badge-status badge-izin">
                                        <i class="fas fa-clock" style="font-size:9px;"></i> Menunggu
                                    </span>
                                @elseif($item->status == 'disetujui')
                                    <span class="badge-status badge-hadir">
                                        <i class="fas fa-check-circle" style="font-size:9px;"></i> Disetujui
                                    </span>
                                @else
                                    <span class="badge-status badge-alpha">
                                        <i class="fas fa-times-circle" style="font-size:9px;"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="/absensi/pengajuan/{{ $item->id_pengajuan }}"
                                   class="btn btn-sm"
                                   style="background: var(--green-50); color: var(--green-700); border: 1px solid var(--green-200); border-radius: var(--radius-sm); font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-eye me-1"></i>Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center"
                                style="padding: 40px; color: var(--gray-400);">
                                <i class="fas fa-folder-open"
                                   style="font-size: 28px; display: block; margin-bottom: 8px; opacity: 0.4;"></i>
                                Belum ada pengajuan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection