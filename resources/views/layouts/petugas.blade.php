<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Petugas Area') - {{ config('app.name', 'SampahKU') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap"
        rel="stylesheet">

    {{-- Icons (Bootstrap + FontAwesome) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --primary-light: #ecfdf5;
            --white: #ffffff;
            --bg: #f9fafb;
            --ink: #111827;
            --muted: #6b7280;
            --line: #e5e7eb;
            --hover-bg: #f8fafc;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --radius: 12px;
            --radius-sm: 8px;
            --sidebar-w: 260px;
            --navbar-h: 70px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg);
            color: var(--ink);
            font-family: "Inter", sans-serif;
            font-size: 14px;
        }

        /* Layout Structure */
        .layout {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Sidebar */
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
            font-size: 18px;
        }

        .brand b {
            font-size: 18px;
            font-weight: 800;
            color: var(--ink);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .brand span {
            font-size: 12px;
            color: var(--muted);
            font-weight: 500;
        }

        .side-scroll {
            padding: 20px 16px;
            overflow-y: auto;
            flex: 1;
        }

        .side-section {
            padding: 12px 12px 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--muted);
            letter-spacing: 0.5px;
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
            transition: var(--transition);
        }

        .side-link:hover {
            background: var(--hover-bg);
            color: var(--primary);
        }

        .side-link.active {
            background: var(--primary);
            color: var(--white);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .side-link.active i {
            color: var(--white);
        }

        .side-link i {
            width: 20px;
            text-align: center;
            color: var(--muted);
            font-size: 16px;
            transition: .2s;
        }

        /* Main Content & Navbar */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
        }

        .navbar {
            height: var(--navbar-h);
            background: var(--white);
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .content {
            padding: 24px;
            flex: 1;
        }

        /* Mobile Overlay */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 900;
            backdrop-filter: blur(2px);
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main {
                margin-left: 0;
            }

            .overlay.show {
                display: block;
            }
        }

        /* NOTIFICATION STYLES */
        .notif-wrapper {
            position: relative;
        }

        .notif-btn {
            background: transparent;
            border: none;
            font-size: 1.25rem;
            color: var(--muted);
            cursor: pointer;
            position: relative;
            padding: 4px;
            transition: color .2s;
        }

        .notif-btn:hover {
            color: var(--primary);
        }

        .notif-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #ef4444;
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            min-width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--white);
        }

        .notif-dropdown {
            position: absolute;
            top: 120%;
            right: 0;
            width: 320px;
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 2000;
            display: none;
            flex-direction: column;
            overflow: hidden;
            margin-top: 10px;
        }

        .notif-dropdown.show {
            display: flex;
            animation: slideDown 0.2s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notif-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--ink);
            background: var(--bg);
        }

        .mark-read {
            font-size: 0.75rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .notif-body {
            max-height: 300px;
            overflow-y: auto;
        }

        .notif-item {
            padding: 12px 16px;
            border-bottom: 1px solid var(--line);
            display: flex;
            gap: 12px;
            cursor: pointer;
            transition: background .2s;
            text-align: left;
            width: 100%;
            background: none;
            border-left: none;
            border-right: none;
            border-top: none;
        }

        .notif-item:hover {
            background: var(--bg);
        }

        .notif-icon {
            width: 36px;
            height: 36px;
            background: var(--primary-light);
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .notif-text {
            flex: 1;
        }

        .notif-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 2px;
        }

        .notif-desc {
            font-size: 0.8rem;
            color: var(--muted);
            line-height: 1.3;
        }

        .notif-time {
            font-size: 0.7rem;
            color: #9ca3af;
            margin-top: 4px;
        }

        .notif-empty {
            padding: 30px;
            text-align: center;
            color: var(--muted);
        }

        .notif-empty i {
            font-size: 2rem;
            margin-bottom: 8px;
            display: block;
        }

        /* USER PROFILE DROPDOWN */
        .user-dropdown {
            position: relative;
        }

        .nav-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary-dark);
            display: grid;
            place-items: center;
            font-weight: 700;
            cursor: pointer;
            border: 2px solid var(--bg);
            box-shadow: var(--shadow-sm);
            transition: all .2s;
            user-select: none;
        }

        .nav-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .user-dropdown-menu {
            position: absolute;
            top: 120%;
            right: 0;
            width: 220px;
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            z-index: 2000;
            display: none;
            flex-direction: column;
            overflow: hidden;
            margin-top: 10px;
        }

        .user-dropdown-menu.show {
            display: flex;
            animation: slideDown 0.2s ease-out;
        }

        .user-dropdown-header {
            padding: 12px 16px;
            background: var(--bg);
            border-bottom: 1px solid var(--line);
        }

        .user-name {
            font-weight: 700;
            color: var(--ink);
            font-size: 0.9rem;
        }

        .user-email {
            font-size: 0.75rem;
            color: var(--muted);
        }

        .user-dropdown-item {
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--ink);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: background .2s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .user-dropdown-item:hover {
            background: var(--hover-bg);
            color: var(--primary);
        }

        .user-dropdown-item i {
            width: 18px;
            text-align: center;
            color: var(--muted);
        }

        .divider {
            height: 1px;
            background: var(--line);
            margin: 4px 0;
        }

        /* Colors Helpers */
        .text-primary {
            color: #10b981;
            background: #ecfdf5;
        }

        .text-blue-500 {
            color: #3b82f6;
            background: #eff6ff;
        }

        .text-green-500 {
            color: #10b981;
            background: #ecfdf5;
        }

        .text-red-500 {
            color: #ef4444;
            background: #fef2f2;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="layout">
        <div class="overlay" id="overlay"></div>

        {{-- SIDEBAR PETUGAS --}}
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                <div class="logo"><i class="fa-solid fa-truck-fast"></i></div>
                <div style="display:flex; flex-direction:column; line-height:1.2;">
                    <b>SampahKU</b>
                    <span>Petugas Area</span>
                </div>
            </div>

            <div class="side-scroll">
                <div class="side-section">Operasional</div>
                <nav class="side-menu">
                    <a href="{{ route('petugas.setoran.index') }}"
                        class="side-link {{ request()->routeIs('petugas.setoran.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <span>Daftar Tugas</span>
                    </a>
                    <a href="{{ route('petugas.map') }}"
                        class="side-link {{ request()->routeIs('petugas.map*') ? 'active' : '' }}">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Peta Rute</span>
                    </a>
                </nav>
            </div>

            <div style="padding: 20px; border-top: 1px solid var(--line);">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="side-link" style="width:100%; color:#ef4444;">
                        <i class="fa-solid fa-right-from-bracket" style="color:#ef4444;"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN --}}
        <div class="main">
            <nav class="navbar">
                <button id="sidebarToggle"
                    style="background:none; border:none; font-size:20px; color:var(--muted); cursor:pointer; display:none;">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div style="font-weight:700; color:var(--ink); font-size:1.1rem;">
                    @yield('title')
                </div>

                <div class="nav-user">
                    {{-- NOTIFIKASI --}}
                    <div class="notif-wrapper">
                        <button type="button" class="notif-btn" onclick="toggleDropdown('notifDropdown', event)"
                            aria-label="Notifikasi">
                            <i class="bi bi-bell"></i>
                            @if (auth()->user()->unreadNotifications->count() > 0)
                                <span class="notif-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                            @endif
                        </button>

                        <div class="notif-dropdown" id="notifDropdown">
                            <div class="notif-header">
                                <span>Notifikasi</span>
                                @if (auth()->user()->unreadNotifications->count() > 0)
                                    <a href="{{ route('notif.read.all') }}" class="mark-read">Tandai dibaca</a>
                                @endif
                            </div>
                            <div class="notif-body">
                                @forelse(auth()->user()->unreadNotifications as $notif)
                                    <form action="{{ route('notif.read', $notif->id) }}" method="POST"
                                        class="notif-item" onclick="this.submit()">
                                        @csrf
                                        <div class="notif-icon {{ $notif->data['color'] ?? 'text-primary' }}">
                                            <i class="fa-solid {{ $notif->data['icon'] ?? 'fa-bell' }}"></i>
                                        </div>
                                        <div class="notif-text">
                                            <div class="notif-title">{{ $notif->data['title'] }}</div>
                                            <div class="notif-desc">{{ $notif->data['message'] }}</div>
                                            <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
                                        </div>
                                    </form>
                                @empty
                                    <div class="notif-empty">
                                        <i class="bi bi-bell-slash"></i>
                                        <p style="font-size:0.85rem; margin:0;">Tidak ada notifikasi baru</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- USER PROFILE DROPDOWN --}}
                    <div class="user-dropdown">
                        <div class="nav-avatar" onclick="toggleDropdown('userDropdown', event)">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>

                        <div class="user-dropdown-menu" id="userDropdown">
                            <div class="user-dropdown-header">
                                <div class="user-name">{{ Auth::user()->name }}</div>
                                <div class="user-email">{{ Auth::user()->email }}</div>
                            </div>

                            <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
                                <i class="bi bi-person"></i> Edit Profil
                            </a>

                            <div class="divider"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="user-dropdown-item">
                                    <i class="bi bi-box-arrow-right" style="color:#ef4444;"></i> <span
                                        style="color:#ef4444;">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <main class="content">
                @if (session('success'))
                    <div
                        style="background:#10b981; color:white; padding:12px; border-radius:8px; margin-bottom:16px; font-weight:500;">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div
                        style="background:#ef4444; color:white; padding:12px; border-radius:8px; margin-bottom:16px; font-weight:500;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Sidebar Logic
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        if (window.innerWidth <= 992) toggle.style.display = 'block';

        function toggleMenu() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        }

        toggle?.addEventListener('click', toggleMenu);
        overlay?.addEventListener('click', toggleMenu);

        // Unified Dropdown Logic
        function toggleDropdown(id, e) {
            e.stopPropagation();
            const el = document.getElementById(id);

            // Close others first
            document.querySelectorAll('.notif-dropdown, .user-dropdown-menu').forEach(d => {
                if (d.id !== id) d.classList.remove('show');
            });

            el.classList.toggle('show');
        }

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            const dropdowns = document.querySelectorAll('.notif-dropdown, .user-dropdown-menu');
            dropdowns.forEach(d => {
                if (d.classList.contains('show') && !d.contains(e.target)) {
                    d.classList.remove('show');
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
