

<?php $__env->startSection('title', 'Data Absensi'); ?>

<?php $__env->startSection('content'); ?>


<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>


<div class="d-flex align-items-center gap-2 mb-4">
    <div style="width:38px;height:38px;border-radius:var(--radius-md);background:var(--jade-soft);color:var(--jade-dark);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fas fa-clipboard-list"></i>
    </div>
    <div>
        <div class="fw-bold" style="font-size:1rem;color:var(--text);">Data Absensi</div>
        <small style="color:var(--text-muted);">Rekap kehadiran semua guru</small>
    </div>
</div>


<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon green">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="info">
                <p>Hadir Hari Ini</p>
                <h4><?php echo e($hadirHariIni ?? 0); ?></h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon yellow">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="info">
                <p>Izin Hari Ini</p>
                <h4><?php echo e($izinHariIni ?? 0); ?></h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon red">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="info">
                <p>Alpha Hari Ini</p>
                <h4><?php echo e($alphaHariIni ?? 0); ?></h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="info">
                <p>Total Data</p>
                <h4><?php echo e(count($absensi)); ?></h4>
            </div>
        </div>
    </div>
</div>


<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-sliders-h me-2 text-jade"></i>Filter Data
    </div>
    <div class="card-body">
        <form action="/admin/absensi" method="GET">
            <div class="row g-3">

                <div class="col-12 col-md-4">
                    <label class="form-label">Bulan</label>
                    <input type="month" name="bulan" class="form-control" value="<?php echo e($bulan); ?>">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Guru</label>
                    <select name="guru_id" class="form-select">
                        <option value="">— Semua Guru —</option>
                        <?php $__currentLoopData = $guru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($item->id_user); ?>"
                                <?php echo e($guruId == $item->id_user ? 'selected' : ''); ?>>
                                <?php echo e($item->nama); ?> (<?php echo e($item->nip); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label d-none d-md-block">&nbsp;</label>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="/admin/absensi"
                           style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-md);border:1.5px solid var(--line);background:var(--surface-2);color:var(--text-muted);font-size:0.875rem;font-weight:500;text-decoration:none;transition:all var(--transition);">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                        <a href="/admin/absensi/export-pdf?bulan=<?php echo e($bulan); ?>"
                           style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-md);border:1px solid #fecaca;background:#fee2e2;color:#991b1b;font-size:0.875rem;font-weight:500;text-decoration:none;transition:all var(--transition);">
                            <i class="fas fa-file-pdf"></i> Semua Guru
                        </a>
                        <?php if($guruId): ?>
                        <a href="/admin/absensi/export-pdf?bulan=<?php echo e($bulan); ?>&guru_id=<?php echo e($guruId); ?>"
                           style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-md);border:1px solid #fde68a;background:#fef3c7;color:#92400e;font-size:0.875rem;font-weight:500;text-decoration:none;transition:all var(--transition);">
                            <i class="fas fa-file-pdf"></i> Guru Ini
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>


<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span>
            <i class="fas fa-table me-2 text-jade"></i>Daftar Absensi
        </span>
        <span class="font-mono"
              style="font-size:0.75rem;color:var(--text-light);background:var(--surface-2);border:1px solid var(--line);padding:2px 10px;border-radius:20px;">
            <?php echo e(count($absensi)); ?> data
        </span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Guru</th>
                    <th>NIP</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $absensi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <span class="font-mono" style="color:var(--text-light);font-size:0.78rem;">
                            <?php echo e($key + 1); ?>

                        </span>
                    </td>
                    <td>
                        <span class="font-mono" style="font-size:0.82rem;">
                            <?php echo e(date('d/m/Y', strtotime($item->tanggal))); ?>

                        </span>
                    </td>
                    <td>
                        <span style="font-weight:600;font-size:0.875rem;">
                            <?php echo e($item->user->nama); ?>

                        </span>
                    </td>
                    <td>
                        <span class="font-mono" style="font-size:0.78rem;color:var(--text-muted);">
                            <?php echo e($item->user->nip); ?>

                        </span>
                    </td>
                    <td>
                        <span class="font-mono" style="font-size:0.82rem;">
                            <?php echo e($item->jam_masuk ?? '—'); ?>

                        </span>
                    </td>
                    <td>
                        <span class="font-mono" style="font-size:0.82rem;">
                            <?php echo e($item->jam_keluar ?? '—'); ?>

                        </span>
                    </td>
                    <td>
                        <?php if($item->status == 'hadir'): ?>
                            <span class="badge" style="background:var(--jade-soft);color:var(--jade-dark);">
                                <i class="fas fa-check me-1"></i>Hadir
                            </span>
                        <?php elseif($item->status == 'terlambat'): ?>
                            <span class="badge" style="background:#fef3c7;color:#92400e;">
                                <i class="fas fa-clock me-1"></i>Terlambat
                            </span>
                        <?php elseif($item->status == 'izin'): ?>
                            <span class="badge" style="background:#dbeafe;color:#1e40af;">
                                <i class="fas fa-envelope me-1"></i>Izin
                            </span>
                        <?php elseif($item->status == 'sakit'): ?>
                            <span class="badge" style="background:#f1f5f9;color:var(--text-muted);">
                                <i class="fas fa-notes-medical me-1"></i>Sakit
                            </span>
                        <?php elseif($item->status == 'alpha'): ?>
                            <span class="badge" style="background:#fee2e2;color:#991b1b;">
                                <i class="fas fa-times me-1"></i>Alpha
                            </span>
                        <?php else: ?>
                            <span class="badge" style="background:var(--surface-2);color:var(--text-muted);">
                                <?php echo e($item->status); ?>

                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="max-width:160px;">
                        <span style="font-size:0.82rem;color:var(--text-muted);display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <?php echo e($item->keterangan ?? '—'); ?>

                        </span>
                    </td>
                    <td>
                        <form action="/admin/absensi/<?php echo e($item->id_absensi); ?>" method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit"
                                    title="Hapus"
                                    style="width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid #fecaca;background:#fee2e2;color:#991b1b;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all var(--transition);">
                                <i class="fas fa-trash-alt" style="font-size:0.75rem;pointer-events:none;"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" style="text-align:center;padding:48px 20px;">
                        <i class="fas fa-inbox" style="font-size:2rem;color:var(--text-light);display:block;margin-bottom:10px;"></i>
                        <span style="color:var(--text-muted);font-size:0.875rem;">
                            Tidak ada data absensi untuk filter ini.
                        </span>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\edu_track\resources\views/admin/absensi/index.blade.php ENDPATH**/ ?>