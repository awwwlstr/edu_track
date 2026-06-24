<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EDU-TRACK</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo.app.png')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <i class="fas fa-user-plus"></i>
            <h3>Daftar Akun Baru</h3>
            <p class="mb-0">Lengkapi form di bawah untuk mendaftar</p>
        </div>
        <div class="register-body">

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="/register" method="POST" id="formRegister" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="nama" class="form-control <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Masukkan nama lengkap" value="<?php echo e(old('nama')); ?>" required>
                    </div>
                    <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="text-danger"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">NIP <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        <input type="text" name="nip" id="inputNip"
                               class="form-control <?php $__errorArgs = ['nip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Masukkan NIP" value="<?php echo e(old('nip')); ?>" required>
                    </div>
                    <?php $__errorArgs = ['nip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="text-danger"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Masukkan email" value="<?php echo e(old('email')); ?>" required>
                    </div>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="text-danger"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Foto Wajah via Kamera -->
                <div class="mb-3">
                    <label class="form-label">Foto Wajah <span class="text-danger">*</span></label>
                    <div class="face-box mb-2" id="faceBox">
                        <div class="face-placeholder" id="facePlaceholder">
                            <i class="fas fa-camera fa-2x mb-2"></i><br>
                            <small>Klik "Buka Kamera" untuk mengambil foto wajah</small>
                        </div>
                        <video id="wajahPreview" autoplay playsinline muted></video>
                        <img id="fotoPreview" alt="Foto wajah">
                    </div>
                    <canvas id="canvasWajah" style="display:none"></canvas>
                    <input type="hidden" name="foto_base64" id="fotoBase64">

                    <div id="statusWajah" class="alert alert-warning py-2 small mb-2">
                        ⚠️ Foto wajah belum diambil
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm flex-fill" onclick="bukaKamera()">
                            <i class="fas fa-camera"></i> Buka Kamera
                        </button>
                        <button type="button" class="btn btn-success btn-sm flex-fill" id="btnAmbilFoto"
                                onclick="ambilFoto()" style="display:none">
                            <i class="fas fa-circle"></i> Ambil Foto
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnUlang"
                                onclick="ulangi()" style="display:none">
                            <i class="fas fa-redo"></i> Ulang
                        </button>
                    </div>
                </div>

                <!-- Foto Profil (upload biasa) -->
                <div class="mb-3">
                    <label class="form-label">Foto Profil</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-image"></i></span>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>
                    <small class="text-muted">Format: JPG, PNG. Maks 2MB.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Minimal 6 karakter" required minlength="6">
                    </div>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="text-danger"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <button type="button" onclick="submitRegister()" class="btn btn-primary w-100">
                    <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
                </button>
            </form>

            <div class="divider"><span>Sudah punya akun?</span></div>
            <a href="/login" class="btn btn-outline-secondary w-100" style="border-radius:10px; padding:12px;">
                <i class="fas fa-sign-in-alt me-2"></i> Login Sekarang
            </a>
        </div>
    </div>
    <div class="text-center mt-4">
        <p class="text-white mb-0"><small>&copy; 2026 Sistem Absensi. All rights reserved.</small></p>
    </div>
</div>

<script>
const PYTHON_API     = 'http://127.0.0.1:5000';
let stream           = null;
let fotoSudahDiambil = false;

async function bukaKamera() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        const video = document.getElementById('wajahPreview');
        video.srcObject = stream;
        video.style.display   = 'block';
        document.getElementById('facePlaceholder').style.display = 'none';
        document.getElementById('fotoPreview').style.display     = 'none';
        document.getElementById('btnAmbilFoto').style.display    = 'inline-block';
        document.getElementById('btnUlang').style.display        = 'none';
        setStatusWajah('info', '📷 Posisikan wajah lalu klik Ambil Foto');
        fotoSudahDiambil = false;
    } catch(e) {
        setStatusWajah('danger', '❌ Gagal akses kamera: ' + e.message);
    }
}

function ambilFoto() {
    const video  = document.getElementById('wajahPreview');
    const canvas = document.getElementById('canvasWajah');
    if (video.videoWidth === 0) { setStatusWajah('danger', '❌ Kamera belum siap'); return; }

    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    const base64 = canvas.toDataURL('image/jpeg', 0.9);
    document.getElementById('fotoBase64').value           = base64;
    document.getElementById('fotoPreview').src            = base64;
    document.getElementById('fotoPreview').style.display  = 'block';
    document.getElementById('wajahPreview').style.display = 'none';
    document.getElementById('btnAmbilFoto').style.display = 'none';
    document.getElementById('btnUlang').style.display     = 'inline-block';

    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }

    setStatusWajah('success', '✅ Foto berhasil diambil, silakan lengkapi form lalu daftar');
    fotoSudahDiambil = true;
}

function ulangi() {
    document.getElementById('fotoBase64').value              = '';
    document.getElementById('fotoPreview').style.display     = 'none';
    document.getElementById('btnUlang').style.display        = 'none';
    document.getElementById('facePlaceholder').style.display = 'block';
    setStatusWajah('warning', '⚠️ Foto wajah belum diambil');
    fotoSudahDiambil = false;
}

async function submitRegister() {
    if (!document.getElementById('formRegister').checkValidity()) {
        alert("Lengkapi form dulu");
        return;
    }

    if (!fotoSudahDiambil) {
        setStatusWajah('danger', '❌ Ambil foto wajah dulu!');
        return;
    }

    const nip  = document.getElementById('inputNip').value.trim();
    const foto = document.getElementById('fotoBase64').value;

    setStatusWajah('info', '⏳ Mendaftarkan wajah...');

    try {
        const res  = await fetch(PYTHON_API + '/face/register-simple', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({
                frame:   foto.split(',')[1],
                user_id: nip
            })
        });

        const data = await res.json();
        console.log('Response:', data);

        if (!data.success) {
            setStatusWajah('danger', '❌ ' + (data.message || data.error));
            return;
        }

        setStatusWajah('success', '✅ Wajah terdaftar! Menyimpan akun...');

    } catch(e) {
        setStatusWajah('danger', '❌ Gagal konek Python API: ' + e.message);
        return;
    }

    document.getElementById('formRegister').submit();
}

function setStatusWajah(type, msg) {
    const el = document.getElementById('statusWajah');
    el.className = 'alert alert-' + type + ' py-2 small mb-2';
    el.innerHTML = msg;
}
</script><?php /**PATH C:\laragon\www\edu_track\resources\views/auth/register.blade.php ENDPATH**/ ?>