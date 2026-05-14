@extends('layouts.kepsek')

@section('title', 'Pengaturan Akun')

@section('content')

<div class="p-4 fade-up">
<div class="row justify-content-center">
<div class="col-lg-8">

    {{-- HEADER --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('kepsek.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="page-header" style="margin:0;">
            <h4 style="margin:0;">Pengaturan Akun</h4>
            <small>Kelola informasi akun kepala sekolah</small>
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

    {{-- FOTO PROFIL --}}
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-image me-2 text-success"></i>Foto Profil
        </div>
        <div class="card-body">
            <form action="{{ route('kepsek.pengaturan.update') }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="d-flex align-items-center gap-4">

                    {{-- PREVIEW FOTO --}}
                    <div id="foto-preview-wrapper" style="flex-shrink:0;">
                        @if($user->foto)
                            <img id="foto-preview"
                                 src="{{ asset('storage/' . $user->foto) }}"
                                 alt="Foto Profil"
                                 style="width:90px;height:90px;border-radius:50%;object-fit:cover;
                                        border:3px solid var(--line);box-shadow:var(--e2);">
                        @else
                            <div id="foto-placeholder"
                                 style="width:90px;height:90px;border-radius:50%;
                                        background:linear-gradient(135deg,#d1fae5,#a7f3d0);
                                        display:flex;align-items:center;justify-content:center;
                                        font-family:'Sora',sans-serif;font-size:30px;font-weight:800;
                                        color:var(--jade-deeper);border:3px solid var(--line);">
                                {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                            </div>
                            <img id="foto-preview" src="" alt=""
                                 style="width:90px;height:90px;border-radius:50%;object-fit:cover;
                                        border:3px solid var(--line);box-shadow:var(--e2);display:none;">
                        @endif
                    </div>

                    {{-- UPLOAD AREA --}}
                    <div class="flex-grow-1">
                        <label class="form-label">Ganti Foto</label>
                        <input type="file" name="foto" id="foto-input"
                               class="form-control @error('foto') is-invalid @enderror"
                               accept="image/jpeg,image/png,image/webp">
                        @error('foto')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        <small class="text-muted d-block mt-1">
                            Format: JPG, PNG, WebP. Maks 2MB.
                            @if($user->foto)
                                <span class="text-success ms-2">
                                    <i class="bi bi-check-circle-fill"></i> Foto sudah diset
                                </span>
                            @endif
                        </small>
                    </div>

                </div>

                {{-- TOMBOL SIMPAN FOTO --}}
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" name="save_foto" value="1" class="btn btn-success btn-sm">
                        <i class="bi bi-upload"></i> Simpan Foto
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- INFORMASI AKUN --}}
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-person-circle me-2 text-success"></i>Informasi Akun
        </div>
        <div class="card-body">
            <form action="{{ route('kepsek.pengaturan.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap"
                               class="form-control @error('nama_lengkap') is-invalid @enderror"
                               value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                        @error('nama_lengkap')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                </div>

                <hr class="my-4">

                <div style="font-size:13.5px;font-weight:700;color:var(--ink);margin-bottom:16px;">
                    <i class="bi bi-shield-lock me-2 text-success"></i>
                    Ganti Password
                    <span style="font-weight:400;color:var(--ink-muted);font-size:12px;">
                        (Opsional — kosongkan jika tidak ingin mengganti)
                    </span>
                </div>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Min. 6 karakter">
                        @error('password')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="Ulangi password baru">
                    </div>

                    <div class="col-12 d-flex justify-content-between pt-2">
                        <a href="{{ route('kepsek.dashboard') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>

                </div>

            </form>
        </div>
    </div>

</div>
</div>
</div>

{{-- PREVIEW JS --}}
<script>
document.getElementById('foto-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(ev) {
        const img = document.getElementById('foto-preview');
        const placeholder = document.getElementById('foto-placeholder');

        img.src = ev.target.result;
        img.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
    };
    reader.readAsDataURL(file);
});
</script>

@endsection