<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Absensi')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.app.png') }}">
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
     {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- App CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body>

    {{-- ======= TOP NAVBAR ======= --}}
    <nav class="navbar navbar-expand-lg navbar-dark app-navbar" id="mainNavbar">
        <div class="container-fluid px-3 px-lg-4">

            {{-- Brand --}}
            <a class="navbar-brand d-flex align-items-center gap-2" href="/absensi">
                <!-- Langsung gunakan img tanpa pembungkus brand-icon -->
                <img src="{{ asset('images/logo.app.png') }}" alt="Logo" class="sidebar-logo-img">

                <span class="brand-text d-none d-sm-inline">EDU-TRACK</span>
            </a>

            {{-- Mobile: hamburger + toggle sidebar --}}
            <div class="d-flex align-items-center gap-2 ms-auto d-lg-none">
                <button class="btn btn-sm btn-glass" id="sidebarToggle" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            {{-- Desktop: right nav --}}
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-1">

                    {{-- Clock pill --}}
                    <li class="nav-item me-2">
                        <span class="clock-pill">
                            <i class="fas fa-clock me-1 opacity-75"></i>
                            <span id="navClock">--:--:--</span>
                        </span>
                    </li>

                    {{-- Notification --}}
                    <li class="nav-item">
                        <a href="/notifikasi" class="nav-icon-btn" title="Notifikasi">
                            <i class="fas fa-bell"></i>
                            @php
                                $unread = \App\Models\Notifikasi::where('id_user', auth()->user()->id_user)
                                          ->where('is_read', 0)->count();
                            @endphp
                            @if($unread > 0)
                                <span class="notif-badge">{{ $unread > 9 ? '9+' : $unread }}</span>
                            @endif
                        </a>
                    </li>

                    {{-- User dropdown --}}
                    <li class="nav-item dropdown">
                        <a class="user-pill dropdown-toggle" href="#" id="navbarDropdown"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                            </div>
                            <span class="user-name d-none d-md-inline">{{ auth()->user()->nama }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end app-dropdown">
                            <li>
                                <div class="dropdown-header-info">
                                    <div class="dh-avatar">{{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}</div>
                                    <div>
                                        <div class="dh-name">{{ auth()->user()->nama }}</div>
                                        <div class="dh-role">{{ auth()->user()->email ?? 'Siswa' }}</div>
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
                                    @if($unread > 0)
                                        <span class="badge bg-danger ms-auto">{{ $unread }}</span>
                                    @endif
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="/logout" method="POST">
                                    @csrf
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

    {{-- ======= LAYOUT WRAPPER ======= --}}
    <div class="app-wrapper">

        {{-- ======= SIDEBAR OVERLAY (mobile) ======= --}}
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        {{-- ======= SIDEBAR ======= --}}
        <aside class="app-sidebar" id="appSidebar">

            {{-- Mobile close button --}}
            <div class="sidebar-header d-lg-none">
                <span class="sidebar-logo">
                    <i class="fas fa-clipboard-check me-2"></i> Absensi
                </span>
                <button class="btn btn-sm sidebar-close" id="sidebarClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="sidebar-nav">
                <div class="nav-section-label">Menu Utama</div>

                <a class="sidebar-link {{ Request::is('absensi') ? 'active' : '' }}" href="/absensi">
                    <span class="link-icon"><i class="fas fa-home"></i></span>
                    <span class="link-text">Dashboard</span>
                </a>

                <a class="sidebar-link {{ Request::is('absensi/rekap') ? 'active' : '' }}" href="/absensi/rekap">
                    <span class="link-icon"><i class="fas fa-history"></i></span>
                    <span class="link-text">Rekap Absensi</span>
                </a>

                <a class="sidebar-link {{ Request::is('absensi/kalender') ? 'active' : '' }}" href="/absensi/kalender">
                    <span class="link-icon"><i class="fas fa-calendar-alt"></i></span>
                    <span class="link-text">Kalender</span>
                </a>

                <a class="sidebar-link {{ Request::is('absensi/pengajuan*') ? 'active' : '' }}" href="/absensi/pengajuan">
                    <span class="link-icon"><i class="fas fa-file-alt"></i></span>
                    <span class="link-text">Pengajuan Izin</span>
                </a>

                <a class="sidebar-link {{ Request::is('jurnal*') ? 'active' : '' }}" href="/jurnal">
                    <span class="link-icon"><i class="fas fa-pen-nib"></i></span>
                    <span class="link-text">Jurnal</span>
                </a>

                <a class="sidebar-link {{ Request::is('profil') ? 'active' : '' }}" href="/profil">
                    <span class="link-icon"><i class="fas fa-user-cog"></i></span>
                    <span class="link-text">Profil</span>
                </a>

            </nav>

            {{-- Sidebar footer --}}
            <div class="sidebar-footer">
                <form action="/logout" method="POST" class="w-100 m-0">
                    @csrf
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

        {{-- ======= MAIN CONTENT ======= --}}
        <main class="app-main">

            {{-- Page Header (optional, setiap view bisa pakai @section('page-title')) --}}
            @hasSection('page-title')
            <div class="page-header">
                <h1 class="page-title">@yield('page-title')</h1>
                @hasSection('page-subtitle')
                    <p class="page-subtitle">@yield('page-subtitle')</p>
                @endif
            </div>
            @endif

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert app-alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert app-alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Page content --}}
            @yield('content')
        </main>
    </div>

    {{-- ======= SCRIPTS ======= --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>