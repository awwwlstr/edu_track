

<?php $__env->startSection('title', 'Dashboard Absensi'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4 justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card card-custom bg-light position-relative overflow-hidden">

            
            <div style="
                position:absolute;
                inset:0;
                background-image: radial-gradient(#1B6B3A20 1px, transparent 1px);
                background-size: 20px 20px;
                opacity:0.4;
            "></div>

            
            <div class="card-body text-center position-relative" style="z-index:2;">
                <h3 class="mb-2 fw-bold" style="color:#1e3a8a">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Dashboard Absensi
                </h3>

                <p class="mb-0 text-muted">
                    Hai <strong style="color:#1e3a8a"><?php echo e(auth()->user()->nama); ?></strong> 👋  
                </p>
                <p class="mb-0 text-muted">
                    Yuk cek kehadiran<br>
                    Tetap semangat mengajar hari ini! 🤗
                </p>
            </div>

        </div>
    </div>
</div>

<!-- Alerts -->
<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Jam & Tanggal -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-custom bg-light">
            <div class="card-body text-center">
                <div id="date" class="text-muted mb-2"></div>
                <div id="clock" class="clock"></div>
            </div>
        </div>
    </div>
</div>

<!-- Status Absensi Hari Ini -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Status Absensi Hari Ini</h5>
            </div>
            <div class="card-body">
                <?php if($absensiHariIni): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Status:</strong>
                                <?php if($absensiHariIni->status == 'hadir'): ?>
                                    <span class="badge bg-success">Hadir</span>
                                <?php elseif($absensiHariIni->status == 'terlambat'): ?>
                                    <span class="badge bg-warning">Terlambat</span>
                                <?php elseif($absensiHariIni->status == 'izin'): ?>
                                    <span class="badge bg-info">Izin</span>
                                <?php elseif($absensiHariIni->status == 'sakit'): ?>
                                    <span class="badge bg-secondary">Sakit</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Jam Masuk:</strong> <?php echo e($absensiHariIni->jam_masuk ?? '-'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Jam Keluar:</strong> <?php echo e($absensiHariIni->jam_keluar ?? 'Belum absen keluar'); ?></p>
                            <?php if($absensiHariIni->keterangan): ?>
                                <p><strong>Keterangan:</strong> <?php echo e($absensiHariIni->keterangan); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted">Anda belum melakukan absensi hari ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Tombol Absensi -->
<div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-hand-pointer text-green"></i>
            <h5 class="mb-0">Tombol Absensi</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <button type="button"
                            class="btn btn-primary btn-custom w-100"
                            onclick="startAbsensi('masuk')"
                            <?php if($absensiHariIni): ?> disabled <?php endif; ?>>
                        <i class="fas fa-sign-in-alt fa-2x d-block mb-2"></i>
                        <strong>MASUK</strong>
                    </button>
                </div>
                <div class="col-md-3 col-6">
                    <button type="button"
                            class="btn btn-warning btn-custom w-100"
                            onclick="startAbsensi('keluar')"
                            <?php if(!$absensiHariIni || $absensiHariIni->jam_keluar): ?> disabled <?php endif; ?>>
                        <i class="fas fa-sign-out-alt fa-2x d-block mb-2"></i>
                        <strong>KELUAR</strong>
                    </button>
                </div>
                <div class="col-md-3 col-6">
                    <button type="button"
                            class="btn btn-info btn-custom w-100"
                            data-bs-toggle="modal" data-bs-target="#modalIzin"
                            <?php if($absensiHariIni): ?> disabled <?php endif; ?>>
                        <i class="fas fa-envelope fa-2x d-block mb-2"></i>
                        <strong>IZIN</strong>
                    </button>
                </div>
                <div class="col-md-3 col-6">
                    <button type="button"
                            class="btn btn-secondary btn-custom w-100"
                            data-bs-toggle="modal" data-bs-target="#modalSakit"
                            <?php if($absensiHariIni): ?> disabled <?php endif; ?>>
                        <i class="fas fa-medkit fa-2x d-block mb-2"></i>
                        <strong>SAKIT</strong>
                    </button>
                </div>
            </div>
        </div>
    </div>

 
    <div class="page-header">
        <div class="page-title" style="font-size: 16px;">
            <i class="fas fa-chart-bar me-2 text-green"></i>Statistik Bulan Ini
            <span style="font-size: 13px; font-weight: 500; color: var(--gray-400); margin-left: 6px;">
                (<?php echo e(date('F Y')); ?>)
            </span>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card hadir">
                <div class="stat-label"><i class="fas fa-check-circle me-1"></i>Hadir</div>
                <div class="stat-value" style="color: var(--color-hadir);"><?php echo e($stats['hadir']); ?></div>
                <small style="color: var(--gray-400); font-size: 11px;">hari</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card izin">
                <div class="stat-label"><i class="fas fa-file-alt me-1"></i>Izin</div>
                <div class="stat-value" style="color: var(--color-izin);"><?php echo e($stats['izin']); ?></div>
                <small style="color: var(--gray-400); font-size: 11px;">hari</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card sakit">
                <div class="stat-label"><i class="fas fa-heartbeat me-1"></i>Sakit</div>
                <div class="stat-value" style="color: var(--color-sakit);"><?php echo e($stats['sakit']); ?></div>
                <small style="color: var(--gray-400); font-size: 11px;">hari</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card" style="border-left-color: var(--teal-500);">
                <div class="stat-label"><i class="fas fa-calendar-check me-1"></i>Total Kehadiran</div>
                <div class="stat-value" style="color: var(--teal-500);">
                    <?php echo e($stats['hadir'] + $stats['izin'] + $stats['sakit']); ?>

                </div>
                <small style="color: var(--gray-400); font-size: 11px;">hari</small>
            </div>
        </div>
    </div>

</div>


<!-- Modal Izin -->
<div class="modal fade" id="modalIzin" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Absen Izin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/absensi/izin" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Keterangan Izin <span class="text-danger">*</span></label>
                        <textarea name="keterangan" class="form-control" rows="3" required placeholder="Masukkan alasan izin..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">Kirim Izin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sakit -->
<div class="modal fade" id="modalSakit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Absen Sakit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/absensi/sakit" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Keterangan Sakit <span class="text-danger">*</span></label>
                        <textarea name="keterangan" class="form-control" rows="3" required placeholder="Masukkan keterangan sakit..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-secondary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kamera -->
<div class="modal fade" id="modalKamera" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="judulModal">
                    <i class="fas fa-camera"></i> Verifikasi Wajah
                </h5>
            </div>
            <div class="modal-body text-center p-3">
                <div style="position:relative; display:inline-block; width:100%;">
                    <video id="videoKamera" width="100%" height="280"
                        style="border-radius:8px; background:#000; object-fit:cover;"
                        autoplay playsinline muted></video>
                    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
                        width:180px; height:220px; border:3px solid rgba(0, 72, 255, 0.6);
                        border-radius:50%; pointer-events:none;"></div>
                </div>
                <canvas id="canvasKamera" style="display:none;"></canvas>
                <div id="statusLiveness" class="alert alert-info mt-3 mb-2">
                    <i class="fas fa-eye"></i> Posisikan wajah Anda, lalu <strong>kedipkan mata</strong> beberapa kali...
                </div>
                <div class="progress mb-2" style="height:20px;">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                        style="width:0%; transition:width 0.3s;" role="progressbar">
                        <span id="progressText">0%</span>
                    </div>
                </div>
                <div id="statusGPS" class="text-muted small">
                    <i class="fas fa-map-marker-alt"></i> Mengambil lokasi GPS...
                </div>
            </div>
            <div class="modal-footer">
                <button id="btnProses" class="btn btn-success" onclick="prosesAbsensi()" disabled>
                    <i class="fas fa-check-circle"></i> Proses Absensi
                </button>
                <button class="btn btn-secondary" onclick="tutupModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </div>
    </div>
</div>

<div id="user-data"
    data-user-id="<?php echo e(auth()->user()->nip); ?>"
    data-csrf="<?php echo e(csrf_token()); ?>"
    style="display:none;"></div>

<script>

const currentUserId = document.getElementById('user-data').dataset.userId;
const csrfToken     = document.getElementById('user-data').dataset.csrf;
const PYTHON_API    = 'http://127.0.0.1:5000';

let stream        = null;
let tipeAbsensi   = null;
let livenessOk    = false;
let photoData     = null;
let currentLat    = null;
let currentLng    = null;
let livenessTimer = null;
let modalInstance = null;
let sessionId     = null;   // ← FIX: simpan session_id dari /liveness/start

// ── Mulai absensi ─────────────────────────────────────────────
async function startAbsensi(tipe) {
    tipeAbsensi = tipe;
    livenessOk  = false;
    photoData   = null;
    sessionId   = null;     // ← reset session setiap kali mulai absensi baru

    document.getElementById('judulModal').innerHTML =
        tipe === 'masuk'
            ? '<i class="fas fa-sign-in-alt"></i> Verifikasi Absen Masuk'
            : '<i class="fas fa-sign-out-alt"></i> Verifikasi Absen Keluar';

    setStatus('info', '<i class="fas fa-map-marker-alt"></i> Mengambil lokasi GPS...');
    setProgress(0);
    document.getElementById('btnProses').disabled      = true;
    document.getElementById('btnProses').innerHTML     = '<i class="fas fa-check-circle"></i> Proses Absensi';
    document.getElementById('statusGPS').innerText     = 'Mengambil lokasi GPS...';

    modalInstance = new bootstrap.Modal(document.getElementById('modalKamera'));
    modalInstance.show();

    if (!navigator.geolocation) {
        setStatus('danger', '❌ Browser tidak mendukung GPS!');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        async (pos) => {
            currentLat = pos.coords.latitude;
            currentLng = pos.coords.longitude;
            document.getElementById('statusGPS').innerText =
                '📍 GPS: ' + currentLat.toFixed(5) + ', ' + currentLng.toFixed(5);

            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: 640, height: 480 }
                });
                document.getElementById('videoKamera').srcObject = stream;

                // ── FIX: simpan session_id dari response /liveness/start ──
                const startRes  = await fetch(PYTHON_API + '/liveness/start', { method: 'POST' });
                if (!startRes.ok) throw new Error('Gagal memulai sesi liveness (HTTP ' + startRes.status + ')');
                const startData = await startRes.json();
                sessionId       = startData.session_id;
                console.log('Response start:', startData);      // ← tambah ini
                console.log('Session ID:', sessionId);          // ← tambah ini
                setStatus('info', '<i class="fas fa-eye"></i> Ikuti instruksi yang muncul...');
                startLivenessDetection();
            } catch (e) {
                setStatus('danger', '❌ Gagal akses kamera: ' + e.message);
            }
        },
        (err) => setStatus('danger', '❌ Gagal ambil GPS: ' + err.message),
        { timeout: 10000, enableHighAccuracy: true }
    );
}

// ── Loop liveness (kirim frame tiap 500ms) ────────────────────
function startLivenessDetection() {
    livenessTimer = setInterval(async () => {
        if (!stream) { clearInterval(livenessTimer); return; }

        const canvas = document.getElementById('canvasKamera');
        const video  = document.getElementById('videoKamera');
        if (video.videoWidth === 0) return;

        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        const frameBase64 = canvas.toDataURL('image/jpeg', 0.8).split(',')[1];

        try {
            const res = await fetch(PYTHON_API + '/liveness/process', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                // ── FIX: sertakan session_id di setiap frame ──
                body:    JSON.stringify({ session_id: sessionId, frame: frameBase64 })
            });

            if (!res.ok) throw new Error('HTTP ' + res.status);
            const json = await res.json();

            if (!json.success) {
                setStatus('warning', '⚠️ ' + (json.error || 'Error'));
                return;
            }

            const data = json.data;

            // ── tampilkan instruksi challenge ──
            if (data.message) {
                const icon = {
                    passed:  '✅',
                    failed:  '❌',
                    spoofed: '🚫',
                    pending: '👁️',
                }[data.result] || '👁️';
                setStatus(
                    data.result === 'passed'  ? 'success' :
                    data.result === 'failed'  ? 'danger'  :
                    data.result === 'spoofed' ? 'danger'  : 'info',
                    icon + ' ' + data.message
                );
            }

            // ── progress bar dari challenge sequence ──
            const ch  = data.challenge;
            const pct = ch.total > 0
                ? Math.round((ch.current_index / ch.total) * 100)
                : 0;
            setProgress(Math.max(pct, Math.round(data.liveness_score * 100)));

            // ── liveness selesai ──
            if (data.result === 'passed') {
                clearInterval(livenessTimer);
                livenessOk = true;
                photoData  = frameBase64;
                setProgress(100);
                setStatus('success', '✅ Liveness OK! Klik <strong>Proses Absensi</strong>.');
                document.getElementById('btnProses').disabled = false;
            }

            // ── liveness gagal / spoof ──
            if (data.result === 'failed' || data.result === 'spoofed') {
                clearInterval(livenessTimer);
                setStatus('danger',
                    data.result === 'spoofed'
                        ? '🚫 Terdeteksi sebagai foto/video. Coba lagi.'
                        : '❌ Liveness gagal. Tutup dan coba lagi.'
                );
            }

        } catch (e) {
            clearInterval(livenessTimer);
            setStatus('danger',
                '⚠️ Gagal komunikasi dengan Python API.<br><small>' + e.message + '</small>'
            );
        }
    }, 500);
}

// ── Proses absensi ke Laravel ─────────────────────────────────
async function prosesAbsensi() {
    if (!livenessOk || !photoData) return;

    document.getElementById('btnProses').disabled  = true;
    document.getElementById('btnProses').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    setStatus('info', '<i class="fas fa-spinner fa-spin"></i> Mencocokkan wajah...');

    try {
        // ── FIX: sertakan session_id (sudah passed) ke /face/verify ──
        const faceRes = await fetch(PYTHON_API + '/face/verify', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({
                session_id: sessionId,      // ← wajib, dan harus sudah passed
                frame:      photoData,
                user_id:    currentUserId
            })
        });

        if (!faceRes.ok) {
            const errData = await faceRes.json().catch(() => ({}));
            // akun terkunci (HTTP 423)
            if (faceRes.status === 423) {
                setStatus('danger', '🔒 ' + (errData.message || 'Akun terkunci sementara.'));
            } else {
                throw new Error(errData.error || 'Face API error ' + faceRes.status);
            }
            resetBtn();
            return;
        }

        const faceData = await faceRes.json();

        if (!faceData.is_match) {
            setStatus('danger',
                '❌ Wajah tidak dikenali! Pastikan wajah Anda sudah terdaftar.<br>' +
                '<small>Similarity: ' + (faceData.similarity ?? '-') + '</small>'
            );
            resetBtn();
            return;
        }

        setStatus('info', '<i class="fas fa-spinner fa-spin"></i> Wajah cocok! Menyimpan absensi...');

        // ── simpan ke Laravel ──
        const laravelRes = await fetch('/absensi/' + tipeAbsensi + '-hybrid', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept':       'application/json'
            },
            body: JSON.stringify({
                foto:            photoData,
                latitude:        currentLat,
                longitude:       currentLng,
                face_confidence: faceData.similarity ?? null
            })
        });

        const result = await laravelRes.json();

        if (result.success) {
            setStatus('success', '✅ ' + (result.message || 'Absensi berhasil!'));
            setTimeout(() => { tutupModal(); location.reload(); }, 1500);
        } else {
            setStatus('danger', '❌ ' + (result.message || 'Gagal menyimpan absensi.'));
            resetBtn();
        }

    } catch (e) {
        setStatus('danger', '❌ Terjadi kesalahan: ' + e.message);
        resetBtn();
    }
}

// ── Tutup modal & stop kamera ─────────────────────────────────
function tutupModal() {
    if (livenessTimer) clearInterval(livenessTimer);
    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
    if (modalInstance) modalInstance.hide();
    sessionId = null;   // ← reset session saat modal ditutup
    resetBtn();
}

// ── Helpers ───────────────────────────────────────────────────
function setStatus(type, html) {
    const el = document.getElementById('statusLiveness');
    el.className = 'alert alert-' + type + ' mt-3 mb-2';
    el.innerHTML = html;
}

function setProgress(pct) {
    pct = Math.min(Math.max(pct, 0), 100);
    document.getElementById('progressBar').style.width = pct + '%';
    document.getElementById('progressText').innerText  = pct + '%';
}

function resetBtn() {
    document.getElementById('btnProses').disabled  = false;
    document.getElementById('btnProses').innerHTML = '<i class="fas fa-check-circle"></i> Proses Absensi';
}

// ── Jam & tanggal ─────────────────────────────────────────────
function updateClock() {
    const now    = new Date();
    const days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const months = ['Januari','Februari','Maret','April','Mei','Juni',
                    'Juli','Agustus','September','Oktober','November','Desember'];
    document.getElementById('date').innerText =
        days[now.getDay()] + ', ' + now.getDate() + ' ' +
        months[now.getMonth()] + ' ' + now.getFullYear();
    document.getElementById('clock').innerText =
        now.toLocaleTimeString('id-ID', { hour12: false });
}
setInterval(updateClock, 1000);
updateClock();

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\edu_track\resources\views/absensi/index.blade.php ENDPATH**/ ?>