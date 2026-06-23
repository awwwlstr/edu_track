<?php $__env->startSection('title', 'Data Evaluasi Jurnal'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 fade-up">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="/admin" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="page-header" style="margin:0;">
            <h4 style="margin:0;">Evaluasi Jurnal Guru</h4>
            <small>Daftar seluruh jurnal yang perlu dievaluasi</small>
        </div>
    </div>

    
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari mata pelajaran / kelas"
                           value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending"  <?php echo e(request('status') === 'pending'  ? 'selected' : ''); ?>>Belum Dievaluasi</option>
                        <option value="dinilai"  <?php echo e(request('status') === 'dinilai'  ? 'selected' : ''); ?>>Sudah Dievaluasi</option>
                        <option value="revisi"   <?php echo e(request('status') === 'revisi'   ? 'selected' : ''); ?>>Revisi</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-journal-text me-2 text-success"></i>Daftar Jurnal</span>
            <span class="badge bg-primary">Total: <?php echo e($jurnal->total()); ?></span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Guru</th>
                        <th>Tanggal</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th class="text-center" style="width:100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $jurnal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="font-weight:600;"><?php echo e(optional($item->user)->nama ?? '—'); ?></td>
                        <td><?php echo e($item->tanggal->format('d M Y')); ?></td>
                        <td><?php echo e($item->mata_pelajaran); ?></td>
                        <td><?php echo e($item->kelas); ?></td>
                        <td>
                            <?php if($item->status === 'dinilai'): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Sudah Dievaluasi</span>
                            <?php elseif($item->status === 'revisi'): ?>
                                <span class="badge bg-danger">Revisi</span>
                            <?php else: ?>
                                <span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo e(route('admin.evaluasi.show', $item->id)); ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                            Belum ada jurnal
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($jurnal->hasPages()): ?>
        <div class="card-footer d-flex justify-content-end">
            <?php echo e($jurnal->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\edu_track\resources\views/admin/evaluasi/index.blade.php ENDPATH**/ ?>