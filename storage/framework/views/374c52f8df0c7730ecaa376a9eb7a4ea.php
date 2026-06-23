

<?php $__env->startSection('title', 'Rekap Absensi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <div class="page-title">
                <i class="fas fa-history me-2 text-green"></i>Rekap Absensi
            </div>
            <div class="page-subtitle">Riwayat kehadiran bulanan</div>
        </div>
    </div>

    
    <div class="card mb-4">
        <div class="card-body" style="padding: 14px 20px;">
            <form action="/absensi/rekap" method="GET" class="d-flex gap-2 align-items-center">
                <i class="fas fa-filter text-green"></i>
                <input type="month" name="bulan" class="form-control" 
                       style="max-width: 200px;" value="<?php echo e($bulan); ?>">
                <button type="submit" class="btn btn-primary btn-sm">
                    Tampilkan
                </button>
            </form>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card hadir">
                <div class="stat-label">
                    <i class="fas fa-check-circle me-1"></i>Hadir
                </div>
                <div class="stat-value" style="color: var(--color-hadir);">
                    <?php echo e($total['hadir']); ?>

                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card" style="border-left-color: var(--color-izin);">
                <div class="stat-label">
                    <i class="fas fa-clock me-1"></i>Terlambat
                </div>
                <div class="stat-value" style="color: var(--color-izin);">
                    <?php echo e($total['terlambat']); ?>

                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card izin">
                <div class="stat-label">
                    <i class="fas fa-file-alt me-1"></i>Izin
                </div>
                <div class="stat-value" style="color: var(--color-sakit);">
                    <?php echo e($total['izin']); ?>

                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card sakit">
                <div class="stat-label">
                    <i class="fas fa-heartbeat me-1"></i>Sakit
                </div>
                <div class="stat-value" style="color: var(--color-sakit);">
                    <?php echo e($total['sakit']); ?>

                </div>
            </div>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-table text-green"></i>
            <span>Riwayat Absensi</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-app mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Hari</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $absensi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="color: var(--gray-400); font-size: 12px;">
                                <?php echo e($key + 1); ?>

                            </td>
                            <td style="font-family: 'DM Mono', monospace; font-size: 13px;">
                                <?php echo e(date('d/m/Y', strtotime($item->tanggal))); ?>

                            </td>
                            <td><?php echo e(\Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd')); ?></td>
                            <td style="font-family: 'DM Mono', monospace; font-size: 13px;">
                                <?php echo e($item->jam_masuk ?? '-'); ?>

                            </td>
                            <td style="font-family: 'DM Mono', monospace; font-size: 13px;">
                                <?php echo e($item->jam_keluar ?? '-'); ?>

                            </td>
                            <td>
                                <?php if($item->status == 'hadir'): ?>
                                    <span class="badge-status badge-hadir">
                                        <i class="fas fa-check-circle" style="font-size: 9px;"></i> Hadir
                                    </span>
                                <?php elseif($item->status == 'terlambat'): ?>
                                    <span class="badge-status badge-izin">
                                        <i class="fas fa-clock" style="font-size: 9px;"></i> Terlambat
                                    </span>
                                <?php elseif($item->status == 'izin'): ?>
                                    <span class="badge-status badge-sakit">
                                        <i class="fas fa-file-alt" style="font-size: 9px;"></i> Izin
                                    </span>
                                <?php elseif($item->status == 'sakit'): ?>
                                    <span class="badge-status" 
                                          style="background: #e0f2fe; color: #075985;">
                                        <i class="fas fa-heartbeat" style="font-size: 9px;"></i> Sakit
                                    </span>
                                <?php else: ?>
                                    <span class="badge-status badge-alpha">
                                        <i class="fas fa-times-circle" style="font-size: 9px;"></i> Alpha
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="color: var(--gray-500); font-size: 13px;">
                                <?php echo e($item->keterangan ?? '-'); ?>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center" 
                                style="padding: 40px; color: var(--gray-400);">
                                <i class="fas fa-inbox" style="font-size: 28px; display: block; margin-bottom: 8px; opacity: 0.4;"></i>
                                Tidak ada data absensi
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\edu_track\resources\views/absensi/rekap.blade.php ENDPATH**/ ?>