<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Petugas') - {{ config('app.name', 'Sampah') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --sidebar-width: 260px;
            --navbar-height: 64px;
        }
        body {
            font-family: 'Figtree', sans-serif;
            margin: 0;
            padding: 0;
            background: #f3f4f6;
        }
        .layout-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: var(--sidebar-width);
            background: #059669;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .sidebar-menu {
            padding: 10px 0;
        }
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.2s;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
        }
        .navbar {
            height: var(--navbar-height);
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .content-wrapper {
            padding: 24px;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="layout-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>🚛 Petugas</h2>
            </div>
            <nav class="sidebar-menu">
                <a href="{{ route('petugas.setoran.index') }}" class="{{ request()->routeIs('petugas.setoran.index') ? 'active' : '' }}">
                    📦 Daftar Setoran
                </a>
                <a href="{{ route('petugas.map') }}" class="{{ request()->routeIs('petugas.map*') ? 'active' : '' }}">
                    🗺️ Peta Setoran
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Navbar -->
            <nav class="navbar">
                <div>
                    <button id="sidebarToggle" style="display:none;background:none;border:none;cursor:pointer;font-size:20px">☰</button>
                </div>
                <div class="navbar-user">
                    <span>{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button type="submit" style="background:#ef4444;color:white;border:none;padding:6px 12px;border-radius:6px;cursor:pointer">
                            Logout
                        </button>
                    </form>
                </div>
            </nav>

            <!-- Content -->
            <div class="content-wrapper">
                @if(session('success'))
                    <div style="background:#10b981;color:white;padding:12px;border-radius:8px;margin-bottom:16px">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div style="background:#ef4444;color:white;padding:12px;border-radius:8px;margin-bottom:16px">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });
        
        // Show toggle button on mobile
        if (window.innerWidth <= 768) {
            document.getElementById('sidebarToggle').style.display = 'block';
        }
        window.addEventListener('resize', function() {
            if (window.innerWidth <= 768) {
                document.getElementById('sidebarToggle').style.display = 'block';
            } else {
                document.getElementById('sidebarToggle').style.display = 'none';
                document.getElementById('sidebar').classList.remove('open');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

