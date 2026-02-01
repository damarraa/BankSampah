<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') • {{ config('app.name', 'SampahKu') }}</title>

    <!-- Bootstrap (wajib kalau mau pakai container-fluid, px-*, d-flex, dll) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --brand: #10b981;
            --brand-light: #d1fae5;
            --brand-lighter: #ecfdf5;
            --brand-dark: #059669;

            --ink: #1f2937;
            --ink-light: #4b5563;
            --muted: #6b7280;
            --muted-light: #9ca3af;

            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --bg-tertiary: #f3f4f6;

            --line: #e5e7eb;

            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.1);

            --radius-sm: 6px;
            --radius: 10px;
            --radius-md: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            font-size: 15px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 0.9375rem;
            line-height: 1.5;
            color: var(--ink);
            background: var(--bg-secondary);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            min-height: 100vh;
        }

        /* ===== NAVBAR ===== */
        .app-navbar {
            background: var(--bg-primary);
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
            width: 100%;
        }

        .navbar-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 0;
            gap: 12px;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--brand-dark);
            transition: color .2s ease;
            white-space: nowrap;
        }

        .navbar-brand:hover {
            color: var(--brand);
        }

        .brand-logo {
            width: 36px;
            height: 36px;
            border-radius: var(--radius);
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.25rem;
            flex: 0 0 auto;
        }

        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            font-weight: 500;
            color: var(--ink-light);
            text-decoration: none;
            border-radius: var(--radius);
            transition: all .2s ease;
            font-size: .9375rem;
            white-space: nowrap;
        }

        .nav-link:hover {
            background: var(--brand-lighter);
            color: var(--brand-dark);
        }

        .nav-link.active {
            background: var(--brand-light);
            color: var(--brand-dark);
            font-weight: 600;
        }

        /* Dropdown (desktop) */
        .nav-dropdown>.nav-link::after {
            content: '▾';
            margin-left: 4px;
            font-size: .875rem;
            transition: transform .2s ease;
        }

        .nav-dropdown:hover>.nav-link::after {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 6px;
            background: var(--bg-primary);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            min-width: 190px;

            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: all .2s ease;
            z-index: 1000;
            padding: 8px 0;
        }

        .nav-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: block;
            padding: 10px 14px;
            color: var(--ink-light);
            text-decoration: none;
            font-weight: 500;
            font-size: .9375rem;
            transition: all .2s ease;
        }

        .dropdown-item:hover {
            background: var(--brand-lighter);
            color: var(--brand-dark);
        }

        /* Search */
        .navbar-search {
            margin: 0 8px;
        }

        .search-wrapper {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            pointer-events: none;
        }

        .search-input {
            width: 200px;
            padding: 8px 12px 8px 36px;
            border: 1px solid var(--line);
            border-radius: 999px;
            font-size: .9375rem;
            color: var(--ink);
            background: var(--bg-secondary);
            transition: all .2s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--brand);
            background: var(--bg-primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            width: 260px;
        }

        /* Actions */
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 0 0 auto;
        }

        .action-icon {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: transparent;
            border: none;
            color: var(--ink-light);
            cursor: pointer;
            transition: all .2s ease;
            text-decoration: none;
        }

        .action-icon:hover {
            background: var(--brand-lighter);
            color: var(--brand-dark);
        }

        .action-icon i {
            font-size: 1.125rem;
        }

        .badge-dot {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            background: var(--brand);
            color: #fff;
            border-radius: 9px;
            font-size: .6875rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--bg-primary);
        }

        /* Avatar */
        .user-dropdown {
            position: relative;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: .875rem;
            cursor: pointer;
            transition: all .2s ease;
            border: 2px solid var(--bg-primary);
            box-shadow: var(--shadow-sm);
            user-select: none;
        }

        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow);
        }

        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            background: var(--bg-primary);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            min-width: 220px;
            padding: 8px 0;

            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: all .2s ease;
            z-index: 1000;
        }

        .user-dropdown:hover .user-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            color: var(--ink);
            text-decoration: none;
            font-weight: 500;
            font-size: .9375rem;
            transition: all .2s ease;
            width: 100%;
        }

        .user-dropdown-item:hover {
            background: var(--brand-lighter);
            color: var(--brand-dark);
        }

        .user-dropdown-item i {
            width: 18px;
            color: var(--muted);
            font-size: 1rem;
        }

        .divider {
            height: 1px;
            background: var(--line);
            margin: 8px 0;
        }

        /* Mobile button */
        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            color: var(--ink);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 8px;
            border-radius: var(--radius-sm);
        }

        .mobile-menu-btn:hover {
            background: var(--bg-tertiary);
        }

        /* Mobile menu */
        .mobile-menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: var(--bg-primary);
            z-index: 2000;
            transform: translateX(-100%);
            transition: transform .3s ease;
            padding: 20px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .mobile-menu.active {
            transform: translateX(0);
        }

        .mobile-menu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--line);
        }

        .mobile-menu-close {
            background: transparent;
            border: none;
            font-size: 1.25rem;
            color: var(--muted);
            cursor: pointer;
            padding: 8px;
        }

        .mobile-user-info {
            padding: 16px;
            background: var(--bg-secondary);
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .mobile-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .mobile-nav .nav-link {
            padding: 12px 16px;
            font-size: 1rem;
            border-radius: var(--radius);
        }

        /* Responsive */
        @media (max-width: 992px) {

            .navbar-menu,
            .navbar-search,
            .navbar-actions {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }
        }

        @media (max-width: 480px) {
            .navbar-brand span:last-child {
                display: none;
            }
        }

        /* Content + Footer */
        .content-container {
            min-height: calc(100vh - 120px);
            padding: 24px 0;
        }

        .app-footer {
            background: var(--bg-primary);
            border-top: 1px solid var(--line);
            padding: 20px 0;
            margin-top: 40px;
        }

        .footer-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--ink);
            font-weight: 600;
            font-size: .9375rem;
        }

        .footer-links {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .footer-link {
            color: var(--muted);
            text-decoration: none;
            font-weight: 500;
            font-size: .875rem;
            transition: color .2s ease;
        }

        .footer-link:hover {
            color: var(--brand-dark);
        }

        .footer-copyright {
            color: var(--muted-light);
            font-size: .8125rem;
        }

        @media (max-width: 768px) {
            .footer-wrapper {
                flex-direction: column;
                text-align: center;
                gap: 12px;
            }

            .footer-links {
                justify-content: center;
            }
        }

        /* Alerts */
        .alert-custom {
            background: var(--brand-lighter);
            border-left: 3px solid var(--brand);
            border-radius: var(--radius);
            padding: 14px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            animation: slideIn .3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-custom i {
            font-size: 1.125rem;
            color: var(--brand-dark);
            margin-top: 2px;
        }

        .alert-content h5 {
            margin-bottom: 4px;
            font-size: .9375rem;
            font-weight: 600;
            color: var(--brand-dark);
        }

        .alert-content p {
            margin: 0;
            font-size: .875rem;
            color: var(--ink-light);
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- NAVBAR -->
    <nav class="app-navbar">
        <!-- metode 1: container-fluid + padding bootstrap -->
        <div class="container-fluid px-3 px-md-4">
            <div class="navbar-wrapper">
                <!-- Brand Logo -->
                <a href="{{ route('user.dashboard') }}" class="navbar-brand">
                    <div class="brand-logo">
                        <i class="bi bi-recycle"></i>
                    </div>
                    <span>SampahKu</span>
                </a>

                <!-- Desktop Navigation Menu -->
                <ul class="navbar-menu">
                    <li class="nav-item">
                        <a href="{{ route('user.dashboard') }}"
                            class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-house"></i> Katalog
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('user.setoran.index') }}"
                            class="nav-link {{ request()->routeIs('user.setoran.index') ? 'active' : '' }}">
                            <i class="bi bi-clock-history"></i> Riwayat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('user.map') }}" class="dropdown-item">
                            <i class="bi bi-map"></i> Peta Lokasi
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('user.statistik.index') }}"
                            class="nav-link {{ request()->routeIs('user.statistik.index') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart"></i> Statistik
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('user.bantuan.index') }}"
                            class="nav-link {{ request()->routeIs('user.bantuan.index') ? 'active' : '' }}">
                            <i class="bi bi-question-circle"></i> Bantuan
                        </a>
                    </li>

                </ul>

                <!-- Search Bar -->
                <div class="navbar-search">
                    <div class="search-wrapper">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="Cari...">
                    </div>
                </div>

                <!-- Action Icons & User Menu -->
                <div class="navbar-actions">
                    <a href="{{ route('user.setoran.create') }}" class="action-icon" title="Buat Setoran">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                    <a href="#" class="action-icon" title="Notifikasi">
                        <i class="bi bi-bell"></i>
                        <span class="badge-dot">3</span>
                    </a>

                    <div class="user-dropdown">
                        <div class="user-avatar" title="{{ auth()->user()->name }}">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>

                        <div class="user-dropdown-menu">
                            <div class="px-3 py-2">
                                <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                <div class="small text-muted">{{ auth()->user()->email }}</div>
                            </div>
                            <div class="divider"></div>
                            <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
                                <i class="bi bi-person"></i> Profil Saya
                            </a>
                            <a href="#" class="user-dropdown-item">
                                <i class="bi bi-gear"></i> Pengaturan
                            </a>
                            <a href="#" class="user-dropdown-item">
                                <i class="bi bi-question-circle"></i> Bantuan
                            </a>
                            <div class="divider"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="user-dropdown-item text-start"
                                    style="border:none;background:none;cursor:pointer;">
                                    <i class="bi bi-box-arrow-right"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button class="mobile-menu-btn" id="mobileMenuButton" type="button" aria-label="Open Menu">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- MOBILE MENU -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <a href="{{ route('user.dashboard') }}" class="navbar-brand">
                <div class="brand-logo">
                    <i class="bi bi-recycle"></i>
                </div>
                <span>SampahKu</span>
            </a>
            <button class="mobile-menu-close" id="mobileMenuClose" type="button" aria-label="Close Menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="mobile-user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div>
                <div class="fw-semibold">{{ auth()->user()->name }}</div>
                <div class="small text-muted">{{ auth()->user()->email }}</div>
            </div>
        </div>

        <nav class="mobile-nav">
            <a href="{{ route('user.dashboard') }}"
                class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house"></i> Dashboard
            </a>
            <a href="{{ route('user.setoran.index') }}"
                class="nav-link {{ request()->routeIs('user.setoran.index') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Riwayat Setoran
            </a>
            <a href="{{ route('user.setoran.create') }}"
                class="nav-link {{ request()->routeIs('user.setoran.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i> Setor Sampah
            </a>

            <div class="nav-dropdown">
                <a href="#" class="nav-link">
                    <i class="bi bi-files"></i> Lainnya
                </a>
                <div class="dropdown-menu">
                    <a href="{{ route('user.map') }}" class="dropdown-item">
                        <i class="bi bi-map"></i> Peta Lokasi
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-bar-chart"></i> Statistik
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-question-circle"></i> Bantuan
                    </a>
                </div>
            </div>

            <a href="{{ route('profile.edit') }}"
                class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <i class="bi bi-person"></i> Profil
            </a>
        </nav>

        <div class="mt-auto pt-4 border-top">
            <div class="search-wrapper mb-3">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="search-input w-100" placeholder="Cari...">
            </div>

            <div class="d-flex gap-2 mb-3">
                <a href="{{ route('user.setoran.create') }}" class="btn btn-success flex-grow-1"
                    style="background: var(--brand); border: none; padding: 10px; border-radius: var(--radius);">
                    <i class="bi bi-plus-lg me-2"></i> Setor Sampah
                </a>
                <a href="#" class="action-icon" style="flex:0 0 auto;">
                    <i class="bi bi-bell"></i>
                    <span class="badge-dot">3</span>
                </a>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary w-100"
                    style="border: 1px solid var(--line); background: transparent; padding: 10px; border-radius: var(--radius);">
                    <i class="bi bi-box-arrow-right me-2"></i> Keluar
                </button>
            </form>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="content-container">
        <!-- metode 1: container-fluid + padding bootstrap -->
        <div class="container-fluid px-3 px-md-4">
            @if (session('success'))
                <div class="alert-custom">
                    <i class="bi bi-check-circle-fill"></i>
                    <div class="alert-content">
                        <h5>Berhasil!</h5>
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert-custom" style="background:#fee;border-left-color:#dc2626;">
                    <i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;"></i>
                    <div class="alert-content">
                        <h5 style="color:#dc2626;">Terjadi Kesalahan</h5>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="app-footer">
        <!-- metode 1: container-fluid + padding bootstrap -->
        <div class="container-fluid px-3 px-md-4">
            <div class="footer-wrapper">
                <a href="{{ route('user.dashboard') }}" class="footer-logo">
                    <div class="brand-logo" style="width:30px;height:30px;font-size:.9375rem;">
                        <i class="bi bi-recycle"></i>
                    </div>
                    <span>SampahKu</span>
                </a>

                <div class="footer-links">
                    <a href="{{ route('user.dashboard') }}" class="footer-link">Dashboard</a>
                    <a href="{{ route('user.setoran.index') }}" class="footer-link">Riwayat</a>
                    <a href="{{ route('user.setoran.create') }}" class="footer-link">Setor Sampah</a>
                    <a href="{{ route('user.map') }}" class="footer-link">Peta</a>
                    <a href="{{ route('profile.edit') }}" class="footer-link">Profil</a>
                </div>

                <div class="footer-copyright">
                    © {{ date('Y') }} SampahKu • Kelola sampah dengan bijak 🌱
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS (optional, tapi bagus untuk komponen) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const mobileMenuClose = document.getElementById('mobileMenuClose');
            const mobileMenu = document.getElementById('mobileMenu');

            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', () => {
                    mobileMenu.classList.add('active');
                    document.body.style.overflow = 'hidden';
                });
            }

            if (mobileMenuClose) {
                mobileMenuClose.addEventListener('click', () => {
                    mobileMenu.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }

            mobileMenu.addEventListener('click', (e) => {
                if (e.target === mobileMenu) {
                    mobileMenu.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                document.querySelectorAll('.alert-custom').forEach(alert => {
                    alert.style.transition = 'opacity 0.3s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 300);
                });
            }, 5000);

            // Escape closes menu
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (mobileMenu.classList.contains('active')) {
                        mobileMenu.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
