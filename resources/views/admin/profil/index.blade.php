@extends('layouts.admin')

@section('title', 'Profil Admin')

@section('content')

    <div class="row">
        <!-- Card Info Profil -->
        <div class="col-lg-4 mb-4">
            <div class="card card-custom">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-id-card"></i> Informasi Profil</h5>
                </div>
                <div class="card-body text-center">
                    @if($user->foto)
                        <img src="{{ asset('storage/profil/' . $user->foto) }}" 
                             class="img-fluid rounded-circle mb-3 border border-3 border-primary" 
                             style="width: 200px; height: 200px; object-fit: cover;" 
                             alt="Foto Profil">
                    @else
                        <div class="mb-3">
                            <i class="fas fa-user-circle text-muted" style="font-size: 200px;"></i>
                        </div>
                    @endif
                    
                    <h4 class="mb-1">{{ $user->nama }}</h4>
                    <p class="text-muted mb-1">
                        <i class="fas fa-shield-alt"></i> {{ ucfirst($user->role) }}
                    </p>
                    <p class="text-muted mb-1">
                        <i class="fas fa-id-badge"></i> NIP: {{ $user->nip ?? '-' }}
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-envelope"></i> {{ $user->email }}
                    </p>
                    
                    <hr>
                    
                    <button type="button" class="btn btn-primary btn-custom w-100" data-bs-toggle="modal" data-bs-target="#modalUploadFoto">
                        <i class="fas fa-camera"></i> Ubah Foto Profil
                    </button>
                </div>
            </div>
        </div>

        <!-- Card Edit Profil & Password -->
        <div class="col-lg-8">
            <!-- Edit Data Profil -->
            <div class="card card-custom mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-edit"></i> Edit Data Profil</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/profil/update" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                           value="{{ old('nama', $user->nama) }}" required>
                                </div>
                                @error('nama')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" 
                                           value="{{ old('nip', $user->nip) }}">
                                </div>
                                @error('nip')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" required>
                                </div>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                    <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-custom">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Ganti Password -->
            <div class="card card-custom">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-key"></i> Ganti Password</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/profil/password" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password_lama" class="form-control" required>
                                </div>
                                @error('password_lama')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input type="password" name="password_baru" class="form-control" required minlength="6">
                                </div>
                                @error('password_baru')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-check"></i></span>
                                    <input type="password" name="password_baru_confirmation" class="form-control" required minlength="6">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning btn-custom">
                            <i class="fas fa-lock"></i> Ganti Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Foto -->
<div class="modal fade" id="modalUploadFoto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Upload Foto Profil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/profil/foto" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <img id="previewFoto" src="{{ $user->foto ? asset('storage/profil/' . $user->foto) : 'https://via.placeholder.com/300x300?text=Preview' }}" 
                             class="img-fluid rounded mb-3" style="max-height: 300px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Foto <span class="text-danger">*</span></label>
                        <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" 
                               accept="image/*" required onchange="previewImage(this)">
                        <small class="text-muted">Format: JPG, PNG (Max: 2MB)</small>
                        @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewFoto').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush