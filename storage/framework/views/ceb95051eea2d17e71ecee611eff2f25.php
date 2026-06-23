<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Sistem Absensi'); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo.app.png')); ?>">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
     
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

    
    <nav class="navbar navbar-expand-lg navbar-dark app-navbar" id="mainNavbar">
        <div class="container-fluid px-3 px-lg-4">

            
            <a class="navbar-brand d-flex align-items-center gap-2" href="/absensi">
                <!-- Langsung gunakan img tanpa pembungkus brand-icon -->
                <img src="<?php echo e(asset('images/logo.app.png')); ?>" alt="Logo" class="sidebar-logo-img">

                <span class="brand-text d-none d-sm-inline">EDU-TRACK</span>
            </a>

            
            <div class="d-flex align-items-center gap-2 ms-auto d-lg-none">
                <button class="btn btn-sm btn-glass" id="sidebarToggle" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-1">

                    
                    <li class="nav-item me-2">
                        <span class="clock-pill">
                            <i class="fas fa-clock me-1 opacity-75"></i>
                            <span id="navClock">--:--:--</span>
                        </span>
                    </li>

                    
                    <li class="nav-item">
                        <a href="/notifikasi" class="nav-icon-btn" title="Notifikasi">
                            <i class="fas fa-bell"></i>
                            <?php
                                $unread = \App\Models\Notifikasi::where('id_user', auth()->user()->id_user)
                                          ->where('is_read', 0)->count();
                            ?>
                            <?php if($unread > 0): ?>
                                <span class="notif-badge"><?php echo e($unread > 9 ? '9+' : $unread); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                    
                    <li class="nav-item dropdown">
                        <a class="user-pill dropdown-toggle" href="#" id="navbarDropdown"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                <?php echo e(strtoupper(substr(auth()->user()->nama, 0, 1))); ?>

                            </div>
                            <span class="user-name d-none d-md-inline"><?php echo e(auth()->user()->nama); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end app-dropdown">
                            <li>
                                <div class="dropdown-header-info">
                                    <div class="dh-avatar"><?php echo e(strtoupper(substr(auth()->user()->nama, 0, 1))); ?></div>
                                    <div>
                                        <div class="dh-name"><?php echo e(auth()->user()->nama); ?></div>
                                        <div class="dh-role"><?php echo e(auth()->user()->email ?? 'Siswa'); ?></div>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item app-dd-item" href="/profil">
                                    <i class="fas fa-user-circle"></i> Profil Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item app-dd-item" href="/notifikasi">
                                    <i class="fas fa-bell"></i> Notifikasi
                                    <?php if($unread > 0): ?>
                                        <span class="badge bg-danger ms-auto"><?php echo e($unread); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="/logout" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="dropdown-item app-dd-item text-danger">
                                        <i class="fas fa-sign-out-alt"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    
    <div class="app-wrapper">

        
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        
        <aside class="app-sidebar" id="appSidebar">

            
            <div class="sidebar-header d-lg-none">
                <span class="sidebar-logo">
                    <i class="fas fa-clipboard-check me-2"></i> Absensi
                </span>
                <button class="btn btn-sm sidebar-close" id="sidebarClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            
            <nav class="sidebar-nav">
                <div class="nav-section-label">Menu Utama</div>

                <a class="sidebar-link <?php echo e(Request::is('absensi') ? 'active' : ''); ?>" href="/absensi">
                    <span class="link-icon"><i class="fas fa-home"></i></span>
                    <span class="link-text">Dashboard</span>
                </a>

                <a class="sidebar-link <?php echo e(Request::is('absensi/rekap') ? 'active' : ''); ?>" href="/absensi/rekap">
                    <span class="link-icon"><i class="fas fa-history"></i></span>
                    <span class="link-text">Rekap Absensi</span>
                </a>

                <a class="sidebar-link <?php echo e(Request::is('absensi/kalender') ? 'active' : ''); ?>" href="/absensi/kalender">
                    <span class="link-icon"><i class="fas fa-calendar-alt"></i></span>
                    <span class="link-text">Kalender</span>
                </a>

                <a class="sidebar-link <?php echo e(Request::is('absensi/pengajuan*') ? 'active' : ''); ?>" href="/absensi/pengajuan">
                    <span class="link-icon"><i class="fas fa-file-alt"></i></span>
                    <span class="link-text">Pengajuan Izin</span>
                </a>

                <a class="sidebar-link <?php echo e(Request::is('jurnal*') ? 'active' : ''); ?>" href="/jurnal">
                    <span class="link-icon"><i class="fas fa-pen-nib"></i></span>
                    <span class="link-text">Jurnal</span>
                </a>

                <a class="sidebar-link <?php echo e(Request::is('profil') ? 'active' : ''); ?>" href="/profil">
                    <span class="link-icon"><i class="fas fa-user-cog"></i></span>
                    <span class="link-text">Profil</span>
                </a>

            </nav>

            
            <div class="sidebar-footer">
                <form action="/logout" method="POST" class="w-100 m-0">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="sidebar-link logout-btn">
                        <span class="link-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </span>
                        <span class="link-text">Logout</span>
                    </button>
                </form>

                <div class="sidebar-clock-box">
                    <div id="sidebarTime" class="sb-time">--:--:--</div>
                    <div id="sidebarDate" class="sb-date">-- --- ----</div>
                </div>
            </div>
        </aside>

        
        <main class="app-main">

            
            <?php if (! empty(trim($__env->yieldContent('page-title')))): ?>
            <div class="page-header">
                <h1 class="page-title"><?php echo $__env->yieldContent('page-title'); ?></h1>
                <?php if (! empty(trim($__env->yieldContent('page-subtitle')))): ?>
                    <p class="page-subtitle"><?php echo $__env->yieldContent('page-subtitle'); ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            
            <?php if(session('success')): ?>
                <div class="alert app-alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert app-alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\laragon\www\edu_track\resources\views/layouts/app.blade.php ENDPATH**/ ?>