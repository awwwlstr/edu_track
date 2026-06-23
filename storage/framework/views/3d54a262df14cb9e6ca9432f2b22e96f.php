

<?php $__env->startSection('title', 'Kelola Pengajuan'); ?>

<?php $__env->startSection('content'); ?>


<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>


<div class="d-flex align-items-center gap-2 mb-4">
    <div style="width:38px;height:38px;border-radius:var(--radius-md);background:var(--jade-soft);color:var(--jade-dark);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fas fa-envelope-open-text"></i>
    </div>
    <div>
        <div class="fw-bold" style="font-size:1rem;color:var(--text);">Kelola Pengajuan</div>
        <small style="color:var(--text-muted);">Pengajuan izin dan sakit guru</small>
    </div>
</div>


<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="icon yellow">
                <i class="fas fa-clock"></i>
            </div>
            <div class="info">
                <p>Menunggu</p>
                <h4><?php echo e($pengajuan->where('status', 'menunggu')->count()); ?></h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="info">
                <p>Disetujui</p>
                <h4><?php echo e($pengajuan->where('status', 'disetujui')->count()); ?></h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="icon red">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="info">
                <p>Ditolak</p>
                <h4><?php echo e($pengajuan->where('status', 'ditolak')->count()); ?></h4>
            </div>
        </div>
    </div>
</div>


<div class="mb-3" style="border-bottom:2px solid var(--line);">
    <div class="d-flex gap-1" id="pengajuanTab" role="tablist">
        <button class="nav-tab-btn active" id="menunggu-tab" data-bs-toggle="tab" data-bs-target="#menunggu" type="button"
                style="padding:10px 18px;border:none;background:none;font-size:0.875rem;font-weight:600;color:var(--text-muted);border-bottom:2px solid transparent;margin-bottom:-2px;cursor:pointer;transition:all var(--transition);border-radius:var(--radius-sm) var(--radius-sm) 0 0;">
            <i class="fas fa-clock me-1"></i> Menunggu
            <span class="badge ms-1" style="background:#fef3c7;color:#92400e;font-size:0.65rem;">
                <?php echo e($pengajuan->where('status', 'menunggu')->count()); ?>

            </span>
        </button>
        <button class="nav-tab-btn" id="disetujui-tab" data-bs-toggle="tab" data-bs-target="#disetujui" type="button"
                style="padding:10px 18px;border:none;background:none;font-size:0.875rem;font-weight:600;color:var(--text-muted);border-bottom:2px solid transparent;margin-bottom:-2px;cursor:pointer;transition:all var(--transition);border-radius:var(--radius-sm) var(--radius-sm) 0 0;">
            <i class="fas fa-check me-1"></i> Disetujui
            <span class="badge ms-1" style="background:var(--jade-soft);color:var(--jade-dark);font-size:0.65rem;">
                <?php echo e($pengajuan->where('status', 'disetujui')->count()); ?>

            </span>
        </button>
        <button class="nav-tab-btn" id="ditolak-tab" data-bs-toggle="tab" data-bs-target="#ditolak" type="button"
                style="padding:10px 18px;border:none;background:none;font-size:0.875rem;font-weight:600;color:var(--text-muted);border-bottom:2px solid transparent;margin-bottom:-2px;cursor:pointer;transition:all var(--transition);border-radius:var(--radius-sm) var(--radius-sm) 0 0;">
            <i class="fas fa-times me-1"></i> Ditolak
            <span class="badge ms-1" style="background:#fee2e2;color:#991b1b;font-size:0.65rem;">
                <?php echo e($pengajuan->where('status', 'ditolak')->count()); ?>

            </span>
        </button>
    </div>
</div>

<div class="tab-content" id="pengajuanTabContent">

    
    <div class="tab-pane fade show active" id="menunggu" role="tabpanel">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fas fa-clock me-2" style="color:var(--warning);"></i>Menunggu Persetujuan</span>
                <span class="font-mono" style="font-size:0.75rem;color:var(--text-light);background:var(--surface-2);border:1px solid var(--line);padding:2px 10px;border-radius:20px;">
                    <?php echo e($pengajuan->where('status', 'menunggu')->count()); ?> data
                </span>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Guru</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Alasan</th>
                            <th>Surat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pengajuan->where('status', 'menunggu'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <span class="font-mono" style="color:var(--text-light);font-size:0.78rem;"><?php echo e($key + 1); ?></span>
                            </td>
                            <td>
                                <span style="font-weight:600;font-size:0.875rem;"><?php echo e($item->user?->nama ?? 'User tidak ditemukan'); ?></span>
                            </td>
                            <td>
                                <?php if($item->jenis == 'izin'): ?>
                                    <span class="badge" style="background:#dbeafe;color:#1e40af;">
                                        <i class="fas fa-envelope me-1"></i>Izin
                                    </span>
                                <?php else: ?>
                                    <span class="badge" style="background:#f1f5f9;color:var(--text-muted);">
                                        <i class="fas fa-notes-medical me-1"></i>Sakit
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="font-mono" style="font-size:0.78rem;">
                                    <?php echo e(date('d/m/Y', strtotime($item->tanggal_mulai))); ?>

                                    <span style="color:var(--text-light);">—</span>
                                    <?php echo e(date('d/m/Y', strtotime($item->tanggal_selesai))); ?>

                                </span>
                            </td>
                            <td style="max-width:180px;">
                                <span style="font-size:0.82rem;color:var(--text-muted);display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <?php echo e(Str::limit($item->alasan, 50)); ?>

                                </span>
                            </td>
                            <td>
                                <?php if($item->surat_keterangan): ?>
                                    <a href="<?php echo e(asset('storage/surat/' . $item->surat_keterangan)); ?>" target="_blank"
                                       style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border-radius:var(--radius-sm);border:1px solid #dbeafe;background:#eff6ff;color:#1e40af;font-size:0.78rem;font-weight:500;text-decoration:none;">
                                        <i class="fas fa-file"></i> Lihat
                                    </a>
                                <?php else: ?>
                                    <span style="color:var(--text-light);font-size:0.82rem;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    
                                    <form action="/admin/pengajuan/<?php echo e($item->id_pengajuan); ?>/approve" method="POST" class="d-inline"
                                          onsubmit="return confirm('Setujui pengajuan ini?')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" title="Setujui"
                                                style="height:32px;padding:0 10px;border-radius:var(--radius-sm);border:1px solid #bbf7d0;background:#dcfce7;color:#166534;cursor:pointer;display:inline-flex;align-items:center;gap:5px;font-size:0.78rem;font-weight:600;transition:all var(--transition);">
                                            <i class="fas fa-check" style="pointer-events:none;"></i> Setujui
                                        </button>
                                    </form>
                                    
                                    <button type="button" title="Tolak"
                                            data-bs-toggle="modal" data-bs-target="#tolakModal<?php echo e($item->id_pengajuan); ?>"
                                            style="height:32px;padding:0 10px;border-radius:var(--radius-sm);border:1px solid #fecaca;background:#fee2e2;color:#991b1b;cursor:pointer;display:inline-flex;align-items:center;gap:5px;font-size:0.78rem;font-weight:600;transition:all var(--transition);">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                </div>

                                
                                <div class="modal fade" id="tolakModal<?php echo e($item->id_pengajuan); ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" style="font-size:0.95rem;font-weight:700;">
                                                    <i class="fas fa-times-circle me-2" style="color:var(--danger);"></i>Tolak Pengajuan
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="/admin/pengajuan/<?php echo e($item->id_pengajuan); ?>/reject" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <div class="modal-body">
                                                    <div class="mb-1" style="font-size:0.82rem;color:var(--text-muted);">
                                                        Pengajuan dari <strong style="color:var(--text);"><?php echo e($item->user?->nama ?? 'User tidak ditemukan'); ?></strong>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Alasan Penolakan</label>
                                                        <textarea name="catatan" class="form-control" rows="3"
                                                                  placeholder="Masukkan alasan penolakan..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm"
                                                            style="background:var(--surface-2);border:1px solid var(--line);color:var(--text-muted);"
                                                            data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-sm"
                                                            style="background:#fee2e2;border:1px solid #fecaca;color:#991b1b;font-weight:600;">
                                                        <i class="fas fa-times me-1"></i>Tolak Pengajuan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;padding:48px 20px;">
                                <i class="fas fa-inbox" style="font-size:2rem;color:var(--text-light);display:block;margin-bottom:10px;"></i>
                                <span style="color:var(--text-muted);font-size:0.875rem;">Tidak ada pengajuan menunggu</span>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="tab-pane fade" id="disetujui" role="tabpanel">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fas fa-check-circle me-2" style="color:var(--jade);"></i>Pengajuan Disetujui</span>
                <span class="font-mono" style="font-size:0.75rem;color:var(--text-light);background:var(--surface-2);border:1px solid var(--line);padding:2px 10px;border-radius:20px;">
                    <?php echo e($pengajuan->where('status', 'disetujui')->count()); ?> data
                </span>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Guru</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Alasan</th>
                            <th>Tanggal Disetujui</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pengajuan->where('status', 'disetujui'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <span class="font-mono" style="color:var(--text-light);font-size:0.78rem;"><?php echo e($key + 1); ?></span>
                            </td>
                            <td>
                                <span style="font-weight:600;font-size:0.875rem;"><?php echo e($item->user->nama); ?></span>
                            </td>
                            <td>
                                <?php if($item->jenis == 'izin'): ?>
                                    <span class="badge" style="background:#dbeafe;color:#1e40af;">
                                        <i class="fas fa-envelope me-1"></i>Izin
                                    </span>
                                <?php else: ?>
                                    <span class="badge" style="background:#f1f5f9;color:var(--text-muted);">
                                        <i class="fas fa-notes-medical me-1"></i>Sakit
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="font-mono" style="font-size:0.78rem;">
                                    <?php echo e(date('d/m/Y', strtotime($item->tanggal_mulai))); ?>

                                    <span style="color:var(--text-light);">—</span>
                                    <?php echo e(date('d/m/Y', strtotime($item->tanggal_selesai))); ?>

                                </span>
                            </td>
                            <td style="max-width:180px;">
                                <span style="font-size:0.82rem;color:var(--text-muted);display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <?php echo e(Str::limit($item->alasan, 50)); ?>

                                </span>
                            </td>
                            <td>
                                <span class="font-mono" style="font-size:0.78rem;color:var(--text-muted);">
                                    <?php echo e(date('d/m/Y H:i', strtotime($item->updated_at))); ?>

                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;padding:48px 20px;">
                                <i class="fas fa-inbox" style="font-size:2rem;color:var(--text-light);display:block;margin-bottom:10px;"></i>
                                <span style="color:var(--text-muted);font-size:0.875rem;">Tidak ada pengajuan disetujui</span>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="tab-pane fade" id="ditolak" role="tabpanel">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fas fa-times-circle me-2" style="color:var(--danger);"></i>Pengajuan Ditolak</span>
                <span class="font-mono" style="font-size:0.75rem;color:var(--text-light);background:var(--surface-2);border:1px solid var(--line);padding:2px 10px;border-radius:20px;">
                    <?php echo e($pengajuan->where('status', 'ditolak')->count()); ?> data
                </span>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Guru</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Alasan</th>
                            <th>Catatan Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pengajuan->where('status', 'ditolak'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <span class="font-mono" style="color:var(--text-light);font-size:0.78rem;"><?php echo e($key + 1); ?></span>
                            </td>
                            <td>
                                <span style="font-weight:600;font-size:0.875rem;"><?php echo e($item->user->nama); ?></span>
                            </td>
                            <td>
                                <?php if($item->jenis == 'izin'): ?>
                                    <span class="badge" style="background:#dbeafe;color:#1e40af;">
                                        <i class="fas fa-envelope me-1"></i>Izin
                                    </span>
                                <?php else: ?>
                                    <span class="badge" style="background:#f1f5f9;color:var(--text-muted);">
                                        <i class="fas fa-notes-medical me-1"></i>Sakit
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="font-mono" style="font-size:0.78rem;">
                                    <?php echo e(date('d/m/Y', strtotime($item->tanggal_mulai))); ?>

                                    <span style="color:var(--text-light);">—</span>
                                    <?php echo e(date('d/m/Y', strtotime($item->tanggal_selesai))); ?>

                                </span>
                            </td>
                            <td style="max-width:180px;">
                                <span style="font-size:0.82rem;color:var(--text-muted);display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <?php echo e(Str::limit($item->alasan, 50)); ?>

                                </span>
                            </td>
                            <td style="max-width:160px;">
                                <span style="font-size:0.82rem;color:var(--text-muted);display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <?php echo e($item->catatan_admin ?? '—'); ?>

                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;padding:48px 20px;">
                                <i class="fas fa-inbox" style="font-size:2rem;color:var(--text-light);display:block;margin-bottom:10px;"></i>
                                <span style="color:var(--text-muted);font-size:0.875rem;">Tidak ada pengajuan ditolak</span>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>


<style>
.nav-tab-btn.active {
    color: var(--jade-dark) !important;
    border-bottom: 2px solid var(--jade) !important;
    background: var(--jade-glow) !important;
}
.nav-tab-btn:hover:not(.active) {
    color: var(--text) !important;
    background: var(--surface-2) !important;
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\edu_track\resources\views/admin/pengajuan/index.blade.php ENDPATH**/ ?>