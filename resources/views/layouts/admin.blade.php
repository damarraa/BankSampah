<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') - {{ config('app.name', 'SampahKU') }}</title>

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap"
        rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --primary-light: #ecfdf5;
            --primary-hover: #34d399;

            --white: #ffffff;
            --bg: #f9fafb;
            --card: #ffffff;
            --ink: #111827;
            --muted: #6b7280;
            --line: #e5e7eb;
            --hover-bg: #f8fafc;

            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);

            --radius: 12px;
            --radius-sm: 8px;
            --radius-lg: 16px;

            --sidebar-w: 260px;
            --navbar-h: 70px;

            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--bg);
            color: var(--ink);
            font-family: "Inter", system-ui, -apple-system, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            font-weight: 400;
        }

        /* ===== LAYOUT ===== */
        .layout {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--white);
            border-right: 1px solid var(--line);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .brand {
            padding: 24px 20px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand .logo {
            width: 42px;
            height: 42px;
            border-radius: var(--radius);
            display: grid;
            place-items: center;
            color: var(--white);
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            flex: 0 0 auto;
            font-size: 18px;
        }

        .brand .title {
            display: flex;
            flex-direction: column;
            line-height: 1.3;
        }

        .brand .title b {
            font-size: 18px;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -0.025em;
        }

        .brand .title span {
            font-size: 12px;
            font-weight: 500;
            color: var(--muted);
            margin-top: 2px;
        }

        .side-scroll {
            padding: 20px 16px;
            overflow: auto;
            flex: 1;
        }

        .side-section {
            padding: 12px 12px 8px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .side-menu {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .side-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--ink);
            font-weight: 500;
            border: 1px solid transparent;
            transition: var(--transition);
        }

        .side-link i {
            width: 20px;
            text-align: center;
            color: var(--muted);
            font-size: 16px;
        }

        .side-link:hover {
            background: var(--hover-bg);
            color: var(--primary);
        }

        .side-link:hover i {
            color: var(--primary);
        }

        .side-link.active {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--white);
            font-weight: 600;
        }

        .side-link.active i {
            color: var(--white);
        }

        /* sidebar footer */
        .sidebar-footer {
            padding: 20px 16px;
            border-top: 1px solid var(--line);
            background: var(--white);
        }

        .mini-user {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .mini-user .who {
            display: flex;
            flex-direction: column;
            line-height: 1.3;
            min-width: 0;
            flex: 1;
        }

        .mini-user .who b {
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mini-user .who span {
            font-weight: 500;
            font-size: 12px;
            color: var(--muted);
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--line);
            background: var(--white);
            color: var(--muted);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            font-size: 13px;
        }

        .btn-logout:hover {
            border-color: #ef4444;
            background: #fef2f2;
            color: #dc2626;
        }

        /* ===== MAIN ===== */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
        }

        /* ===== NAVBAR ===== */
        .navbar {
            height: var(--navbar-h);
            background: var(--white);
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 200;
            display: flex;
            align-items: center;
            box-shadow: var(--shadow-sm);
        }

        .nav-inner {
            width: 100%;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
            flex: 1;
        }

        .burger {
            display: none;
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--line);
            background: var(--white);
            cursor: pointer;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            flex: 0 0 auto;
            color: var(--muted);
        }

        .burger:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* SEARCH BAR */
        .search-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            max-width: 500px;
        }

        .search {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: var(--radius);
            border: 1px solid var(--line);
            background: var(--white);
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .search:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .search .ico {
            color: var(--muted);
            font-size: 16px;
        }

        .search input {
            width: 100%;
            border: none;
            outline: none;
            font-weight: 500;
            background: transparent;
            color: var(--ink);
            font-size: 14px;
        }

        .search input::placeholder {
            color: var(--muted);
            font-weight: 400;
        }

        .kbd {
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid var(--line);
            background: var(--bg);
            color: var(--muted);
            font-weight: 500;
            font-size: 12px;
            white-space: nowrap;
        }

        .search .clear {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            border: 1px solid transparent;
            background: transparent;
            cursor: pointer;
            display: grid;
            place-items: center;
            color: var(--muted);
            transition: var(--transition);
        }

        .search .clear:hover {
            background: var(--hover-bg);
            color: var(--ink);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: flex-end;
            flex: 0 0 auto;
        }

        /* PROFILE BUTTON */
        .profile {
            position: relative;
        }

        .profile-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            border-radius: var(--radius);
            border: 1px solid var(--line);
            background: var(--white);
            cursor: pointer;
            transition: var(--transition);
        }

        .profile-btn:hover {
            border-color: var(--primary);
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: var(--radius);
            display: grid;
            place-items: center;
            background: var(--primary-light);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 14px;
            flex: 0 0 auto;
        }

        .profile-btn .name {
            display: flex;
            flex-direction: column;
            line-height: 1.3;
            max-width: 180px;
        }

        .profile-btn .name b {
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-btn .name span {
            font-weight: 500;
            font-size: 12px;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            min-width: 220px;
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            padding: 8px;
            display: none;
            z-index: 999;
        }

        .profile-menu.show {
            display: block;
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pm-link,
        .pm-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            border: 1px solid transparent;
            background: transparent;
            color: var(--ink);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            font-size: 14px;
        }

        .pm-link i,
        .pm-btn i {
            width: 18px;
            text-align: center;
            color: var(--muted);
            font-size: 16px;
        }

        .pm-link:hover,
        .pm-btn:hover {
            background: var(--hover-bg);
        }

        .pm-danger {
            color: #dc2626;
        }

        .pm-danger i {
            color: #dc2626;
        }

        .pm-danger:hover {
            background: #fef2f2;
        }

        /* ===== CONTENT ===== */
        .content {
            padding: 24px;
            flex: 1;
        }

        /* Flash Messages */
        .flash {
            padding: 16px 20px;
            border-radius: var(--radius);
            font-weight: 500;
            margin-bottom: 24px;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
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

        .flash.success {
            background: var(--primary-light);
            border-color: rgba(16, 185, 129, 0.2);
            color: var(--primary-dark);
        }

        .flash.error {
            background: #fef2f2;
            border-color: rgba(239, 68, 68, 0.2);
            color: #991b1b;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 8px;
        }

        .page-subtitle {
            font-size: 14px;
            color: var(--muted);
        }

        /* Card Design */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--line);
            box-shadow: var(--shadow-sm);
            padding: 24px;
            margin-bottom: 24px;
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--ink);
        }

        /* ===== MOBILE ===== */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 900;
            backdrop-filter: blur(3px);
        }

        @media (max-width: 992px) {
            .burger {
                display: flex;
            }

            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .overlay.show {
                display: block;
            }

            .main {
                margin-left: 0;
            }

            .search-wrap {
                max-width: 100%;
            }

            .profile-btn .name {
                display: none;
            }

            .content {
                padding: 16px;
            }
        }

        @media (max-width: 768px) {
            .nav-inner {
                padding: 0 16px;
            }

            .kbd {
                display: none;
            }
        }

        /* Utility Classes */
        .text-primary {
            color: var(--primary);
        }

        .bg-primary {
            background: var(--primary);
        }

        .text-white {
            color: var(--white);
        }

        .mb-4 {
            margin-bottom: 16px;
        }

        .mt-4 {
            margin-top: 16px;
        }

        .p-4 {
            padding: 16px;
        }

        .rounded {
            border-radius: var(--radius);
        }

        .shadow {
            box-shadow: var(--shadow);
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="layout">
        <div class="overlay" id="overlay"></div>

        {{-- SIDEBAR --}}
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                <div class="logo"><i class="fa-solid fa-leaf"></i></div>
                <div class="title">
                    <b>SampahKU</b>
                    <span>Admin Panel</span>
                </div>
            </div>

            <div class="side-scroll">
                <div class="side-section">Menu Utama</div>

                <nav class="side-menu">
                    <a href="{{ route('admin.dashboard') }}"
                        class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.setoran.index') }}"
                        class="side-link {{ request()->routeIs('admin.setoran.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-box-archive"></i>
                        <span>Data Setoran</span>
                    </a>

                    <a href="{{ route('admin.map') }}"
                        class="side-link {{ request()->routeIs('admin.map*') ? 'active' : '' }}">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Peta Setoran</span>
                    </a>
                </nav>

                <div class="side-section">Master Data</div>

                <nav class="side-menu">
                    <a href="{{ route('master_kategori_sampah.index') }}"
                        class="side-link {{ request()->routeIs('master_kategori_sampah.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Master Kategori</span>
                    </a>

                    <a href="{{ route('kategori_sampah.index') }}"
                        class="side-link {{ request()->routeIs('kategori_sampah.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-tags"></i>
                        <span>Kategori Sampah</span>
                    </a>

                    <a href="{{ route('admin.stok.index') }}"
                        class="side-link {{ request()->routeIs('admin.stok.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-warehouse"></i>
                        <span>Stok Gudang</span>
                    </a>

                    <a href="{{ route('admin.karya.index') }}"
                        class="side-link {{ request()->routeIs('admin.karya.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span>Produksi Karya</span>
                    </a>

                    <a href="{{ route('admin.penjualan.index') }}"
                        class="side-link {{ request()->routeIs('admin.penjualan.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                        <span>Barang Keluar</span>
                    </a>
                </nav>
            </div>

            {{-- <div class="sidebar-footer">
        <div class="mini-user">
          <div class="who">
            <b>{{ Auth::user()->name }}</b>
            <span>Administrator</span>
          </div>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
              <i class="fa-solid fa-right-from-bracket"></i>
              Logout
            </button>
          </form>
        </div>
      </div> --}}
        </aside>

        {{-- MAIN --}}
        <div class="main">
            {{-- NAVBAR --}}
            <nav class="navbar">
                <div class="nav-inner">
                    <div class="nav-left">
                        <button class="burger" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
                            <i class="fa-solid fa-bars"></i>
                        </button>

                        <div class="search-wrap">
                            <div class="search" title="Tekan Enter untuk mencari">
                                <i class="fa-solid fa-magnifying-glass ico"></i>

                                <input type="text" id="globalSearch"
                                    placeholder="Cari setoran, kategori, atau data lain..." autocomplete="off" />

                                <button type="button" class="clear" id="searchClear" aria-label="Clear">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>

                                <span class="kbd" id="kbdHint">Ctrl K</span>
                            </div>
                        </div>
                    </div>

                    {{-- PROFILE --}}
                    <div class="nav-actions">
                        <div class="profile" id="profileWrap">
                            <button type="button" class="profile-btn" id="profileBtn" aria-label="Profile menu">
                                <div class="avatar"><i class="fa-solid fa-user"></i></div>
                                <div class="name">
                                    <b>{{ Auth::user()->name }}</b>
                                    <span>Admin</span>
                                </div>
                                <i class="fa-solid fa-chevron-down" style="color: var(--muted)"></i>
                            </button>

                            <div class="profile-menu" id="profileMenu">
                                <a class="pm-link" href="{{ route('profile.edit') }}">
                                    <i class="fa-solid fa-user-circle"></i>
                                    <span>Profile</span>
                                </a>

                                {{-- <a class="pm-link" href="{{ route('admin.dashboard') }}">
                  <i class="fa-solid fa-gauge-high"></i>
                  <span>Dashboard</span>
                </a> --}}

                                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="pm-btn pm-danger">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </nav>

            {{-- CONTENT --}}
            <main class="content">
                @if (session('success'))
                    <div class="flash success">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="flash error">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Sidebar mobile
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const toggle = document.getElementById('sidebarToggle');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        toggle?.addEventListener('click', () => {
            if (sidebar.classList.contains('open')) closeSidebar();
            else openSidebar();
        });

        overlay?.addEventListener('click', closeSidebar);

        window.addEventListener('resize', () => {
            if (window.innerWidth > 992) closeSidebar();
        });

        // Search UX upgrades
        const searchInput = document.getElementById('globalSearch');
        const searchClear = document.getElementById('searchClear');
        const kbdHint = document.getElementById('kbdHint');

        // Windows/Linux = Ctrl+K, Mac = ⌘K
        const isMac = /Mac|iPhone|iPad|iPod/i.test(navigator.platform);
        kbdHint.textContent = isMac ? '⌘ K' : 'Ctrl K';

        function doSearch(q) {
            const value = (q || '').trim();
            if (!value) return;
            // Arahkan ke halaman setoran dengan query
            window.location.href = "{{ route('admin.setoran.index') }}" + "?q=" + encodeURIComponent(value);
        }

        searchClear?.addEventListener('click', () => {
            searchInput.value = '';
            searchInput.focus();
        });

        searchInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                doSearch(searchInput.value);
            }
        });

        // Ctrl+K / Cmd+K focus search
        window.addEventListener('keydown', (e) => {
            const k = e.key?.toLowerCase();
            if ((e.ctrlKey || e.metaKey) && k === 'k') {
                e.preventDefault();
                searchInput?.focus();
            }
            if (e.key === 'Escape') {
                // close profile menu if open
                document.getElementById('profileMenu')?.classList.remove('show');
            }
        });

        // Profile dropdown
        const profileBtn = document.getElementById('profileBtn');
        const profileMenu = document.getElementById('profileMenu');

        profileBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!profileBtn?.contains(e.target) && !profileMenu?.contains(e.target)) {
                profileMenu?.classList.remove('show');
            }
        });

        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            const flashes = document.querySelectorAll('.flash');
            flashes.forEach(flash => {
                flash.style.transition = 'opacity 0.5s ease';
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 500);
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>

</html>
