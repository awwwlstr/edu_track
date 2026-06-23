

<?php $__env->startSection('title', 'Profil Saya'); ?>

<?php $__env->startSection('content'); ?>


<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-times-circle me-1"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>


<div class="d-flex align-items-center gap-2 mb-4">
    <div style="width:38px;height:38px;border-radius:var(--radius-md);background:var(--jade-soft);color:var(--jade-dark);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fas fa-user"></i>
    </div>
    <div>
        <div class="fw-bold" style="font-size:1rem;color:var(--text);">Profil Saya</div>
        <small style="color:var(--text-muted);">Kelola informasi akun dan keamanan</small>
    </div>
</div>


<div class="row g-4">

    
    <div class="col-12 col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-camera me-2 text-jade"></i>Foto Profil
            </div>
            <div class="card-body text-center d-flex flex-column align-items-center justify-content-center" style="gap:16px;">

                
                <?php if(isset($user->foto) && $user->foto): ?>
                    <img src="<?php echo e(asset('fotoprofil/' . $user->foto)); ?>"
                         class="rounded-circle"
                         style="width:100px;height:100px;object-fit:cover;border:3px solid var(--jade-soft);"
                         alt="Foto Profil"
                         onerror="this.onerror=null;this.style.display='none';document.getElementById('avatar-initials').style.display='flex';">
                    <div id="avatar-initials"
                         style="display:none;width:100px;height:100px;border-radius:50%;background:var(--jade);color:white;align-items:center;justify-content:center;font-size:2rem;font-weight:700;letter-spacing:2px;">
                        <?php echo e(strtoupper(substr($user->nama, 0, 2))); ?>

                    </div>
                <?php else: ?>
                    <div style="width:100px;height:100px;border-radius:50%;background:var(--jade);color:white;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;letter-spacing:2px;">
                        <?php echo e(strtoupper(substr($user->nama, 0, 2))); ?>

                    </div>
                <?php endif; ?>

                <div>
                    <div class="fw-bold" style="font-size:0.95rem;color:var(--text);"><?php echo e($user->nama); ?></div>
                    <small style="color:var(--text-muted);"><?php echo e($user->email); ?></small>
                </div>

                
                <form action="/profil/foto" method="POST" enctype="multipart/form-data" style="width:100%;">
                    <?php echo csrf_field(); ?>
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

    
    <div class="col-12 col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-edit me-2 text-jade"></i>Edit Data Profil
            </div>
            <div class="card-body">
                <form action="/profil/update" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label class="form-label">
                            Nama Lengkap <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="text" name="nama"
                               class="form-control <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('nama', $user->nama)); ?>"
                               required>
                        <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip"
                               class="form-control <?php $__errorArgs = ['nip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('nip', $user->nip)); ?>">
                        <?php $__errorArgs = ['nip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Email <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="email" name="email"
                               class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('email', $user->email)); ?>"
                               required>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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

        
        <div class="card">
            <div class="card-header">
                <i class="fas fa-key me-2 text-jade"></i>Ganti Password
            </div>
            <div class="card-body">
                <form action="/profil/password" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label class="form-label">
                            Password Lama <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="password" name="password_lama"
                               class="form-control <?php $__errorArgs = ['password_lama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Masukkan password saat ini"
                               required>
                        <?php $__errorArgs = ['password_lama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Password Baru <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="password" name="password_baru"
                               class="form-control <?php $__errorArgs = ['password_baru'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Minimal 6 karakter"
                               required>
                        <?php $__errorArgs = ['password_baru'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\edu_track\resources\views/profil/index.blade.php ENDPATH**/ ?>