

<?php $__env->startSection('title', 'Kalender Absensi'); ?>

<?php $__env->startSection('content'); ?>
<?php
    use Carbon\Carbon;

    $tanggalAwal = Carbon::parse($bulan . '-01');
    $jumlahHari  = $tanggalAwal->daysInMonth;
    $hariPertama = $tanggalAwal->dayOfWeek;
?>

<div class="container-fluid">

    
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <div class="page-title">
                <i class="fas fa-calendar-alt me-2 text-green"></i>Kalender Absensi
            </div>
            <div class="page-subtitle"><?php echo e($tanggalAwal->translatedFormat('F Y')); ?></div>
        </div>
        <div class="d-flex gap-2">
            <a href="?bulan=<?php echo e($tanggalAwal->copy()->subMonth()->format('Y-m')); ?>"
               class="btn btn-secondary btn-sm">
                <i class="fas fa-chevron-left"></i> Sebelumnya
            </a>
            <a href="?bulan=<?php echo e($tanggalAwal->copy()->addMonth()->format('Y-m')); ?>"
               class="btn btn-secondary btn-sm">
                Berikutnya <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-calendar-alt me-2 text-green"></i>Kalender Absensi</span>
            <span style="font-size:12px;color:var(--gray-400);font-weight:500;">
                <?php echo e($tanggalAwal->translatedFormat('F Y')); ?>

            </span>
        </div>
        <div class="card-body p-3">

            
            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;text-align:center;margin-bottom:4px;">
                <?php $__currentLoopData = ['Min','Sen','Sel','Rab','Kam','Jum','Sab']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $hari): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="
                        font-size:11px;
                        font-weight:700;
                        text-transform:uppercase;
                        letter-spacing:0.5px;
                        padding:6px 0;
                    "><?php echo e($hari); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;text-align:center;margin-top:4px;">

                
                <?php for($i = 0; $i < $hariPertama; $i++): ?>
                    <div></div>
                <?php endfor; ?>

                
                <?php for($tgl = 1; $tgl <= $jumlahHari; $tgl++): ?>
                    <?php
                        $tanggalFull  = sprintf('%s-%02d', $bulan, $tgl);
                        $tanggalObj   = Carbon::parse($tanggalFull);
                        $dayOfWeek    = $tanggalObj->dayOfWeek;
                        $keyHariBesar = sprintf('%02d', $tgl);

                        $data         = $absensi[$tanggalFull] ?? null;
                        $isWeekend    = in_array($dayOfWeek, [0, 6]);
                        $isLibur      = isset($hariBesar[$keyHariBesar]);
                        $namaLibur    = $isLibur ? $hariBesar[$keyHariBesar] : '';
                        $isToday      = $tanggalObj->isToday();

                        // Warna berdasarkan status
                        if ($data) {
                            if (in_array($data->status, ['hadir', 'terlambat'])) {
                                $bg       = 'var(--green-100)';
                                $color    = 'var(--green-800)';
                                $dotColor = 'var(--color-hadir)';
                                $label    = ucfirst($data->status);
                            } elseif ($data->status === 'izin') {
                                $bg       = '#fef9c3';
                                $color    = '#854d0e';
                                $dotColor = 'var(--color-izin)';
                                $label    = 'Izin';
                            } elseif ($data->status === 'sakit') {
                                $bg       = '#e0f2fe';
                                $color    = '#075985';
                                $dotColor = 'var(--color-sakit)';
                                $label    = 'Sakit';
                            } else {
                                $bg       = '#fee2e2';
                                $color    = '#991b1b';
                                $dotColor = 'var(--color-alpha)';
                                $label    = 'Alpha';
                            }
                        } elseif ($isLibur) {
                            $bg       = 'var(--green-50)';
                            $color    = 'var(--green-700)';
                            $dotColor = 'var(--green-400)';
                            $label    = $namaLibur;
                        } elseif ($isWeekend) {
                            $bg       = 'var(--gray-100)';
                            $color    = 'var(--gray-400)';
                            $dotColor = 'var(--gray-300)';
                            $label    = $dayOfWeek == 0 ? 'Minggu' : 'Sabtu';
                        } else {
                            $bg       = '#fff';
                            $color    = 'var(--gray-700)';
                            $dotColor = '';
                            $label    = '';
                        }

                        // Today override
                        if ($isToday && !$data) {
                            $bg    = 'var(--green-600)';
                            $color = '#fff';
                        }
                    ?>

                    <div style="
                        background: <?php echo e($bg); ?>;
                        color: <?php echo e($color); ?>;
                        border-radius: var(--radius-md);
                        border: 1px solid <?php echo e($isToday && !$data ? 'var(--green-600)' : 'var(--gray-100)'); ?>;
                        min-height: 64px;
                        padding: 6px 4px;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: flex-start;
                        gap: 3px;
                        font-size: 12px;
                        transition: box-shadow var(--dur) var(--ease), transform var(--dur) var(--ease);
                        cursor: default;
                        box-shadow: var(--shadow-sm);
                    "
                    onmouseenter="this.style.boxShadow='var(--shadow-md)';this.style.transform='translateY(-1px)'"
                    onmouseleave="this.style.boxShadow='var(--shadow-sm)';this.style.transform='translateY(0)'">

                        
                        <span style="
                            font-weight:700;
                            font-size:13px;
                            font-family:'DM Mono',monospace;
                            line-height:1;
                        "><?php echo e($tgl); ?></span>

                        
                        <?php if($data): ?>
                            <span class="badge-status" style="
                                background:<?php echo e($bg); ?>;
                                color:<?php echo e($color); ?>;
                                font-size:9px;
                                padding:2px 5px;
                                border-radius:20px;
                                font-weight:600;
                                border:1px solid <?php echo e($color); ?>;
                                opacity:0.85;
                            "><?php echo e($label); ?></span>
                        <?php elseif($isLibur): ?>
                            <span style="font-size:9px;font-weight:600;line-height:1.2;text-align:center;word-break:break-word;">
                                🎉 <?php echo e(Str::limit($namaLibur, 12)); ?>

                            </span>
                        <?php elseif($isWeekend): ?>
                            <span style="font-size:9px;color:var(--gray-400);"><?php echo e($label); ?></span>
                        <?php endif; ?>

                    </div>
                <?php endfor; ?>

            </div>

            
            <div class="mt-3 pt-3 d-flex gap-2 flex-wrap" style="border-top:1px solid var(--gray-100);">
                <span class="badge-status badge-hadir" style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;">
                    <i class="fas fa-circle me-1" style="font-size:7px;color:var(--color-hadir);"></i>Hadir / Terlambat
                </span>
                <span class="badge-status badge-izin" style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;">
                    <i class="fas fa-circle me-1" style="font-size:7px;color:var(--color-izin);"></i>Izin
                </span>
                <span class="badge-status badge-sakit" style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;">
                    <i class="fas fa-circle me-1" style="font-size:7px;color:var(--color-sakit);"></i>Sakit
                </span>
                <span class="badge-status badge-alpha" style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;">
                    <i class="fas fa-circle me-1" style="font-size:7px;color:var(--color-alpha);"></i>Alpha
                </span>
                <span class="badge-status" style="background:var(--gray-100);color:var(--gray-500);padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;">
                    <i class="fas fa-circle me-1" style="font-size:7px;color:var(--gray-300);"></i>Sabtu / Minggu
                </span>
                <span class="badge-status" style="background:var(--green-50);color:var(--green-700);padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;">
                    🎉 Hari Besar
                </span>
            </div>

        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\edu_track\resources\views/absensi/kalender.blade.php ENDPATH**/ ?>