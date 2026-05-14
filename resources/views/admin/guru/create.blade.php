@extends('layouts.admin')

@section('title', 'Tambah Guru')

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-2">
        <div style="width:38px;height:38px;border-radius:var(--radius-md);background:var(--jade-soft);color:var(--jade-dark);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fas fa-user-plus"></i>
        </div>
        <div>
            <div class="fw-bold" style="font-size:1rem;color:var(--text);">Tambah Guru Baru</div>
            <small style="color:var(--text-muted);">Isi data lengkap guru di bawah ini</small>
        </div>
    </div>
    <a href="/admin/guru"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-md);border:1.5px solid var(--line);background:var(--surface-2);color:var(--text-muted);font-size:0.875rem;font-weight:500;text-decoration:none;transition:all var(--transition);">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

{{-- Form Card --}}
<div class="row">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-edit me-2 text-jade"></i>Form Tambah Guru
            </div>
            <div class="card-body">
                <form action="/admin/guru" method="POST">
                    @csrf

                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Nama Lengkap <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="text" name="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama') }}"
                               placeholder="Contoh: Budi Santoso, S.Pd"
                               required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- NIP --}}
                    <div class="mb-3">
                        <label class="form-label">
                            NIP <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="text" name="nip"
                               class="form-control @error('nip') is-invalid @enderror"
                               value="{{ old('nip') }}"
                               placeholder="Contoh: 198501012010011001"
                               required>
                        @error('nip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Email <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="Contoh: budi@sekolah.sch.id"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label class="form-label">
                            Password <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Minimal 6 karakter"
                               required minlength="6">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small style="color:var(--text-muted);font-size:0.78rem;">
                            <i class="fas fa-info-circle me-1"></i>Minimal 6 karakter
                        </small>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                        <a href="/admin/guru"
                           style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-md);border:1.5px solid var(--line);background:var(--surface-2);color:var(--text-muted);font-size:0.875rem;font-weight:500;text-decoration:none;transition:all var(--transition);">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection