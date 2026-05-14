<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Sistem Absensi')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.admin.app.png') }}">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @stack('styles')
</head>

<body>

<div class="app-wrapper">

    <!-- SIDEBAR -->
    <aside id="sidebar">
             <h5>
              <!-- Cukup tambah spasi lalu tulis nama class barunya -->
                     <img src="{{ asset('images/logo.admin.app.png') }}" alt="Logo" class="sidebar-logo-img me-2 rounded shadow-sm">
                <span>EDU-TRACK</span>
            </h5>
        <ul class="nav flex-column">

            <li>
                <a class="nav-link {{ Request::is('admin') ? 'active' : '' }}" href="/admin">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a class="nav-link {{ Request::is('admin/guru*') ? 'active' : '' }}" href="/admin/guru">
                    <i class="fas fa-users"></i>
                    <span>Kelola Guru</span>
                </a>
            </li>

            <li>
                <a class="nav-link {{ Request::is('admin/absensi*') ? 'active' : '' }}" href="/admin/absensi">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Absensi</span>
                </a>
            </li>

            <li>
                <a class="nav-link {{ Request::is('admin/pengajuan*') ? 'active' : '' }}" href="/admin/pengajuan">
                    <i class="fas fa-envelope-open-text"></i>
                    <span>Pengajuan</span>

                    @php
                        $pending = \App\Models\PengajuanIzin::where('status', 'menunggu')->count();
                    @endphp

                    @if($pending > 0)
                        <span class="badge bg-danger ms-auto">{{ $pending }}</span>
                    @endif
                </a>
            </li>

            <li>
                <a class="nav-link {{ Request::is('admin/evaluasi*') ? 'active' : '' }}" href="{{ route('admin.evaluasi.index') }}">
                    <i class="fas fa-pen-nib"></i>
                    <span>Evaluasi</span>
                </a>
            </li>

        </ul>

        <hr>

        <form action="/logout" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </aside>

    <!-- MAIN -->
    <main>

        <!-- TOPBAR -->
        <div class="topbar">

            <div class="left">
                <!-- tombol mobile -->
                <button id="toggleSidebar" class="btn btn-sm btn-light d-md-none">
                    <i class="fas fa-bars"></i>
                </button>

                <div>
                    <div class="fw-semibold">@yield('title')</div>
                    <small>Selamat datang 👋</small>
                </div>
            </div>

            <div class="right">
                <i class="fas fa-user-circle"></i>
                <span>{{ auth()->user()->nama }}</span>
            </div>

        </div>

        <!-- CONTENT -->
        <div class="content">
            @yield('content')
        </div>

    </main>

</div>

<!-- OVERLAY (WAJIB untuk mobile) -->
<div id="overlay"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="{{ asset('js/admin.js') }}"></script>

@stack('scripts')

</body>
</html>