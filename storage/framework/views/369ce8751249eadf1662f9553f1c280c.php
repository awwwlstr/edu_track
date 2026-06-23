

<?php $__env->startSection('title', 'Kelola Guru'); ?>

<?php $__env->startSection('content'); ?>


<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>


<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-2">
        <div style="width:38px;height:38px;border-radius:var(--radius-md);background:var(--jade-soft);color:var(--jade-dark);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div class="fw-bold" style="font-size:1rem;color:var(--text);">Kelola Data Guru</div>
            <small style="color:var(--text-muted);">Manajemen akun guru aktif</small>
        </div>
    </div>
    <a href="/admin/guru/create" class="btn btn-primary">
        <i class="fas fa-plus-circle me-1"></i> Tambah Guru
    </a>
</div>


<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span>
            <i class="fas fa-table me-2 text-jade"></i>Daftar Guru
        </span>
        <span class="font-mono"
              style="font-size:0.75rem;color:var(--text-light);background:var(--surface-2);border:1px solid var(--line);padding:2px 10px;border-radius:20px;">
            <?php echo e(count($guru)); ?> guru
        </span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $guru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <span class="font-mono" style="color:var(--text-light);font-size:0.78rem;">
                            <?php echo e($key + 1); ?>

                        </span>
                    </td>
                    <td>
                        <?php if($item->foto): ?>
                            <img src="<?php echo e(asset('fotoprofil/' . $item->foto)); ?>"
                                 class="rounded-circle"
                                 style="width:40px;height:40px;object-fit:cover;border:2px solid var(--jade-soft);"
                                 alt="Foto"
                                 onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?php echo e(urlencode($item->nama)); ?>&size=40&background=10b981&color=fff'">
                        <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($item->nama)); ?>&size=40&background=10b981&color=fff"
                                 class="rounded-circle"
                                 style="width:40px;height:40px;border:2px solid var(--jade-soft);"
                                 alt="Avatar">
                        <?php endif; ?>
                    </td>
                    <td>
                        <span style="font-weight:600;font-size:0.875rem;"><?php echo e($item->nama); ?></span>
                    </td>
                    <td>
                        <span class="font-mono" style="font-size:0.78rem;color:var(--text-muted);">
                            <?php echo e($item->nip); ?>

                        </span>
                    </td>
                    <td>
                        <span style="font-size:0.875rem;color:var(--text-muted);"><?php echo e($item->email); ?></span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="/admin/guru/<?php echo e($item->id_user); ?>/edit"
                               style="width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid #fde68a;background:#fef3c7;color:#92400e;display:inline-flex;align-items:center;justify-content:center;transition:all var(--transition);text-decoration:none;"
                               title="Edit">
                                <i class="fas fa-edit" style="font-size:0.75rem;"></i>
                            </a>
                            <form action="/admin/guru/<?php echo e($item->id_user); ?>" method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus guru ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit"
                                        title="Hapus"
                                        style="width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid #fecaca;background:#fee2e2;color:#991b1b;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all var(--transition);">
                                    <i class="fas fa-trash-alt" style="font-size:0.75rem;pointer-events:none;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px 20px;">
                        <i class="fas fa-inbox" style="font-size:2rem;color:var(--text-light);display:block;margin-bottom:10px;"></i>
                        <span style="color:var(--text-muted);font-size:0.875rem;">
                            Belum ada data guru. <a href="/admin/guru/create" style="color:var(--jade);">Tambah sekarang</a>
                        </span>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>



<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\edu_track\resources\views/admin/guru/index.blade.php ENDPATH**/ ?>