

<?php $__env->startSection('title', 'Detail Pengajuan'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-file-alt"></i> Detail Pengajuan</h2>
            <a href="/absensi/pengajuan" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Pengajuan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Jenis</th>
                            <td>
                                <?php if($pengajuan->jenis == 'izin'): ?>
                                    <span class="badge bg-info">Izin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Sakit</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Mulai</th>
                            <td><?php echo e(date('d F Y', strtotime($pengajuan->tanggal_mulai))); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Selesai</th>
                            <td><?php echo e(date('d F Y', strtotime($pengajuan->tanggal_selesai))); ?></td>
                        </tr>
                        <tr>
                            <th>Alasan</th>
                            <td><?php echo e($pengajuan->alasan); ?></td>
                        </tr>
                        <tr>
                            <th>Surat Keterangan</th>
                            <td>
                                <?php if($pengajuan->surat_keterangan): ?>
                                    <a href="<?php echo e(asset('storage/surat/' . $pengajuan->surat_keterangan)); ?>" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-download"></i> Lihat Surat
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?php if($pengajuan->status == 'menunggu'): ?>
                                    <span class="badge bg-warning">Menunggu Persetujuan</span>
                                <?php elseif($pengajuan->status == 'disetujui'): ?>
                                    <span class="badge bg-success">Disetujui</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if($pengajuan->catatan_admin): ?>
                        <tr>
                            <th>Catatan Admin</th>
                            <td><?php echo e($pengajuan->catatan_admin); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Tanggal Pengajuan</th>
                            <td><?php echo e(date('d F Y H:i', strtotime($pengajuan->created_at))); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\edu_track\resources\views/pengajuan/show.blade.php ENDPATH**/ ?>