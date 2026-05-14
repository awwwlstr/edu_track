@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')

{{-- Alert --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-times-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Page Header --}}
<div class="d-flex align-items-center gap-2 mb-4">
    <div style="width:38px;height:38px;border-radius:var(--radius-md);background:var(--jade-soft);color:var(--jade-dark);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fas fa-user"></i>
    </div>
    <div>
        <div class="fw-bold" style="font-size:1rem;color:var(--text);">Profil Saya</div>
        <small style="color:var(--text-muted);">Kelola informasi akun dan keamanan</small>
    </div>
</div>

{{-- ROW UTAMA --}}
<div class="row g-4">

    {{-- FOTO PROFIL --}}
    <div class="col-12 col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-camera me-2 text-jade"></i>Foto Profil
            </div>
            <div class="card-body text-center d-flex flex-column align-items-center justify-content-center" style="gap:16px;">

                {{-- Avatar --}}
                @if(isset($user->foto) && $user->foto)
                    <img src="{{ asset('storage/profil/' . $user->foto) }}"
                         class="rounded-circle"
                         style="width:100px;height:100px;object-fit:cover;border:3px solid var(--jade-soft);"
                         alt="Foto Profil"
                         onerror="this.onerror=null;this.style.display='none';document.getElementById('avatar-initials').style.display='flex';">
                    <div id="avatar-initials"
                         style="display:none;width:100px;height:100px;border-radius:50%;background:var(--jade);color:white;align-items:center;justify-content:center;font-size:2rem;font-weight:700;letter-spacing:2px;">
                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                    </div>
                @else
                    <div style="width:100px;height:100px;border-radius:50%;background:var(--jade);color:white;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;letter-spacing:2px;">
                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                    </div>
                @endif

                <div>
                    <div class="fw-bold" style="font-size:0.95rem;color:var(--text);">{{ $user->nama }}</div>
                    <small style="color:var(--text-muted);">{{ $user->email }}</small>
                </div>

                {{-- Upload Form --}}
                <form action="/profil/foto" method="POST" enctype="multipart/form-data" style="width:100%;">
                    @csrf
                    <div class="mb-2">
                        <input type="file" name="foto" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-upload me-1"></i> Upload Foto
                    </button>
                </form>

            </div>
        </div>
    </div>

    {{-- EDIT PROFIL --}}
    <div class="col-12 col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-edit me-2 text-jade"></i>Edit Data Profil
            </div>
            <div class="card-body">
                <form action="/profil/update" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">
                            Nama Lengkap <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="text" name="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama', $user->nama) }}"
                               required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip"
                               class="form-control @error('nip') is-invalid @enderror"
                               value="{{ old('nip', $user->nip) }}">
                        @error('nip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Email <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control"
                               value="Guru" disabled
                               style="background:var(--surface-2);color:var(--text-muted);">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>

                </form>
            </div>
        </div>

        {{-- GANTI PASSWORD --}}
        <div class="card">
            <div class="card-header">
                <i class="fas fa-key me-2 text-jade"></i>Ganti Password
            </div>
            <div class="card-body">
                <form action="/profil/password" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">
                            Password Lama <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="password" name="password_lama"
                               class="form-control @error('password_lama') is-invalid @enderror"
                               placeholder="Masukkan password saat ini"
                               required>
                        @error('password_lama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Password Baru <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="password" name="password_baru"
                               class="form-control @error('password_baru') is-invalid @enderror"
                               placeholder="Minimal 6 karakter"
                               required>
                        @error('password_baru')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            Konfirmasi Password Baru <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="password" name="password_baru_confirmation"
                               class="form-control"
                               placeholder="Ulangi password baru"
                               required>
                    </div>

                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:var(--radius-md);border:1px solid #fde68a;background:#fef3c7;color:#92400e;font-size:0.875rem;font-weight:600;cursor:pointer;transition:all var(--transition);">
                        <i class="fas fa-lock"></i> Ubah Password
                    </button>

                </form>
            </div>
        </div>

    </div>
</div>

@endsection