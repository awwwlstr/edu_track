@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="row mb-4">
        <div class="col-12">
            <h4><i class="fas fa-user"></i> Profil Saya</h4>
        </div>
    </div>

    <!-- ROW UTAMA -->
    <div class="row">

        <!-- FOTO PROFIL -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-camera"></i> Foto Profil
                </div>
                <div class="card-body text-center">

                    <div class="rounded-circle bg-primary text-white mx-auto mb-3"
                         style="width:120px;height:120px;
                                display:flex;align-items:center;justify-content:center;
                                font-size:40px;">
                        {{ strtoupper(substr($user->nama,0,2)) }}
                    </div>

                    <form action="/profil/foto" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="foto" class="form-control mb-2" required>
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-upload"></i> Upload Foto
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <!-- EDIT PROFIL -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-edit"></i> Edit Data Profil
                </div>
                <div class="card-body">

                    <form action="/profil/update" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control"
                                   value="{{ $user->nama }}" required>
                        </div>

                        <div class="mb-3">
                            <label>NIP</label>
                            <input type="text" name="nip" class="form-control"
                                   value="{{ $user->nip }}">
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ $user->email }}" required>
                        </div>

                        <div class="mb-3">
                            <label>Role</label>
                            <input type="text" class="form-control" value="Guru" disabled>
                        </div>

                        <button class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>

                    </form>

                </div>
            </div>
        </div>

    </div> <!-- END ROW UTAMA -->


    <!-- GANTI PASSWORD -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-key"></i> Ganti Password
                </div>
                <div class="card-body">

                    <form action="/profil/password" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label>Password Lama</label>
                            <input type="password" name="password_lama" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password Baru</label>
                            <input type="password" name="password_baru" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="password_baru_confirmation"
                                   class="form-control" required>
                        </div>

                        <button class="btn btn-warning">
                            <i class="fas fa-lock"></i> Ubah Password
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection
