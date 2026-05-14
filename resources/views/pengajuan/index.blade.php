@extends('layouts.app')

@section('title', 'Pengajuan Izin/Sakit')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-file-alt"></i> Pengajuan Izin/Sakit</h2>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form Pengajuan Baru -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Ajukan Izin/Sakit Baru</h5>
                </div>
                <div class="card-body">
                    <form action="/absensi/pengajuan" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis <span class="text-danger">*</span></label>
                                <select name="jenis" class="form-select" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                </select>
                                @error('jenis')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" class="form-control" required>
                                @error('tanggal_mulai')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_selesai" class="form-control" required>
                                @error('tanggal_selesai')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-8 mb-3">
                                <label class="form-label">Alasan <span class="text-danger">*</span></label>
                                <textarea name="alasan" class="form-control" rows="3" required placeholder="Jelaskan alasan izin/sakit..."></textarea>
                                @error('alasan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Surat Keterangan (Opsional)</label>
                                <input type="file" name="surat_keterangan" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, PNG (Max 2MB)</small>
                                @error('surat_keterangan')
                                    <small class="text-danger d-block">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-custom">
                            <i class="fas fa-paper-plane"></i> Kirim Pengajuan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Pengajuan -->
    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Riwayat Pengajuan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
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
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        @if($item->jenis == 'izin')
                                            <span class="badge bg-info">Izin</span>
                                        @else
                                            <span class="badge bg-secondary">Sakit</span>
                                        @endif
                                    </td>
                                    <td>{{ date('d/m/Y', strtotime($item->tanggal_mulai)) }} - {{ date('d/m/Y', strtotime($item->tanggal_selesai)) }}</td>
                                    <td>{{ Str::limit($item->alasan, 50) }}</td>
                                    <td>
                                        @if($item->status == 'menunggu')
                                            <span class="badge bg-warning">Menunggu</span>
                                        @elseif($item->status == 'disetujui')
                                            <span class="badge bg-success">Disetujui</span>
                                        @else
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="/absensi/pengajuan/{{ $item->id_pengajuan }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada pengajuan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection