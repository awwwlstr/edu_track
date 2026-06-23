

<?php $__env->startSection('title', 'Dashboard Admin'); ?>

<?php $__env->startSection('content'); ?>


<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom stat-card primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Guru</div>
                    <div class="fs-4 fw-bold"><?php echo e($totalGuru); ?></div>
                </div>
                <i class="fas fa-users fa-2x text-primary opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Hadir Hari Ini</div>
                    <div class="fs-4 fw-bold text-success"><?php echo e($hadirHariIni); ?></div>
                </div>
                <i class="fas fa-user-check fa-2x text-success opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Izin Hari Ini</div>
                    <div class="fs-4 fw-bold text-warning"><?php echo e($izinHariIni); ?></div>
                </div>
                <i class="fas fa-file-alt fa-2x text-warning opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom stat-card danger">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Alpha Hari Ini</div>
                    <div class="fs-4 fw-bold text-danger"><?php echo e($alphaHariIni); ?></div>
                </div>
                <i class="fas fa-user-times fa-2x text-danger opacity-50"></i>
            </div>
        </div>
    </div>
</div>


<div class="row g-3 mb-4">
    <div class="col-12">
        <h6 class="fw-bold text-muted">
            <i class="fas fa-journal-whills me-2"></i>Statistik Jurnal Mengajar
        </h6>
    </div>
    <div class="col-md-3">
        <div class="card card-custom stat-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Jurnal</div>
                    <div class="fs-4 fw-bold text-info"><?php echo e($totalJurnal); ?></div>
                </div>
                <i class="fas fa-book fa-2x text-info opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Belum Dievaluasi</div>
                    <div class="fs-4 fw-bold text-warning"><?php echo e($jurnalPending); ?></div>
                </div>
                <i class="fas fa-clock fa-2x text-warning opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Sudah Dievaluasi</div>
                    <div class="fs-4 fw-bold text-success"><?php echo e($jurnalDinilai); ?></div>
                </div>
                <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom stat-card danger">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Perlu Revisi</div>
                    <div class="fs-4 fw-bold text-danger"><?php echo e($jurnalRevisi); ?></div>
                </div>
                <i class="fas fa-redo fa-2x text-danger opacity-50"></i>
            </div>
        </div>
    </div>
</div>


<?php if($pengajuanMenunggu > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-exclamation-triangle"></i>
    <span>Ada <strong><?php echo e($pengajuanMenunggu); ?></strong> pengajuan izin yang menunggu persetujuan.</span>
    <a href="/admin/pengajuan" class="btn btn-warning btn-sm ms-auto">Lihat Sekarang</a>
</div>
<?php endif; ?>


<div class="card card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-clipboard-list me-2"></i>Absensi Terbaru Hari Ini</span>
        <a href="/admin/absensi" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nama Guru</th>
                    <th>Jam Masuk</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $absensiTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e(optional($item->user)->nama ?? '—'); ?></td>
                    <td><?php echo e($item->jam_masuk); ?></td>
                    <td>
                        <?php if($item->status === 'hadir'): ?>
                            <span class="badge bg-success">Hadir</span>
                        <?php elseif($item->status === 'terlambat'): ?>
                            <span class="badge bg-warning">Terlambat</span>
                        <?php elseif($item->status === 'izin'): ?>
                            <span class="badge bg-info">Izin</span>
                        <?php elseif($item->status === 'sakit'): ?>
                            <span class="badge bg-secondary">Sakit</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Alpha</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="3" class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x d-block mb-2 opacity-25"></i>
                        Belum ada absensi hari ini
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\edu_track\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>