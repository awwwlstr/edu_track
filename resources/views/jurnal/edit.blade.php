@extends('layouts.app')

@section('title', 'Edit Jurnal')

@section('content')

<div class="p-4 fade-up">

    <div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- HEADER --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('jurnal.show', $jurnal->id) }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="page-header" style="margin:0;">
                <h4 style="margin:0;">Edit Jurnal</h4>
                <small>{{ $jurnal->mata_pelajaran }} — {{ $jurnal->kelas }}</small>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-2 text-success"></i>Edit Jurnal Mengajar
            </div>
            <div class="card-body">

                @if($errors->any())
                    <div class="alert alert-danger mb-4">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <strong>Terdapat kesalahan:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('jurnal.update', $jurnal->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        {{-- TANGGAL --}}
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal"
                                   class="form-control @error('tanggal') is-invalid @enderror"
                                   value="{{ old('tanggal', $jurnal->tanggal->format('Y-m-d')) }}"
                                   required>
                            @error('tanggal')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        {{-- JAM PELAJARAN --}}
                        <div class="col-md-6">
                            <label class="form-label">Jam Pelajaran</label>
                            <input type="text" name="jam_pelajaran"
                                   class="form-control @error('jam_pelajaran') is-invalid @enderror"
                                   placeholder="Contoh: 08.00 – 09.30"
                                   value="{{ old('jam_pelajaran', $jurnal->jam_pelajaran) }}"
                                   required>
                            @error('jam_pelajaran')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        {{-- MATA PELAJARAN --}}
                        <div class="col-md-6">
                            <label class="form-label">Mata Pelajaran</label>
                            <input type="text" name="mata_pelajaran"
                                   class="form-control @error('mata_pelajaran') is-invalid @enderror"
                                   value="{{ old('mata_pelajaran', $jurnal->mata_pelajaran) }}"
                                   required>
                            @error('mata_pelajaran')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        {{-- KELAS --}}
                        <div class="col-md-6">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="kelas"
                                   class="form-control @error('kelas') is-invalid @enderror"
                                   value="{{ old('kelas', $jurnal->kelas) }}"
                                   required>
                            @error('kelas')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        {{-- MATERI --}}
                        <div class="col-12">
                            <label class="form-label">Materi Pembelajaran</label>
                            <textarea name="materi" rows="4"
                                      class="form-control @error('materi') is-invalid @enderror"
                                      required>{{ old('materi', $jurnal->materi) }}</textarea>
                            @error('materi')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        {{-- KENDALA --}}
                        <div class="col-12">
                            <label class="form-label">
                                Kendala
                                <span style="font-weight:400;color:var(--text-muted);font-size:12px;">(Opsional)</span>
                            </label>
                            <textarea name="kendala" rows="3"
                                      class="form-control">{{ old('kendala', $jurnal->kendala) }}</textarea>
                        </div>

                        {{-- BUTTON --}}
                        <div class="col-12 d-flex justify-content-between pt-2">
                            <a href="{{ route('jurnal.show', $jurnal->id) }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i> Update Jurnal
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>

    </div>
    </div>

</div>

@endsection