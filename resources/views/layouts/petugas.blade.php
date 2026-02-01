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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">

  {{-- Icons --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />

  {{-- Styles (Menggunakan Basis Layout Admin) --}}
  <style>
    :root{
      --primary: #10b981; --primary-dark: #059669; --primary-light: #ecfdf5;
      --white: #ffffff; --bg: #f9fafb; --ink: #111827; --muted: #6b7280; --line: #e5e7eb; --hover-bg: #f8fafc;
      --shadow-sm: 0 1px 3px rgba(0,0,0,0.1); --shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
      --radius: 12px; --radius-sm: 8px;
      --sidebar-w: 260px; --navbar-h: 70px;
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: var(--bg); color: var(--ink); font-family: "Inter", sans-serif; font-size: 14px; }

    /* Layout Structure */
    .layout { display: flex; min-height: 100vh; position: relative; }

    /* Sidebar */
    .sidebar {
      width: var(--sidebar-w); background: var(--white); border-right: 1px solid var(--line);
      position: fixed; top: 0; bottom: 0; left: 0; z-index: 1000;
      display: flex; flex-direction: column; transition: var(--transition);
    }

    .brand {
      padding: 24px 20px; border-bottom: 1px solid var(--line); display: flex; align-items: center; gap: 12px;
    }
    .brand .logo {
        width: 42px; height: 42px; border-radius: var(--radius); display: grid; place-items: center;
        color: var(--white); background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        font-size: 18px;
    }
    .brand b { font-size: 18px; font-weight: 800; color: var(--ink); font-family: 'Plus Jakarta Sans', sans-serif; }
    .brand span { font-size: 12px; color: var(--muted); font-weight: 500; }

    .side-scroll { padding: 20px 16px; overflow-y: auto; flex: 1; }
    .side-section { padding: 12px 12px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--muted); letter-spacing: 0.5px; }
    .side-menu { display: flex; flex-direction: column; gap: 4px; }

    .side-link {
        display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: var(--radius-sm);
        text-decoration: none; color: var(--ink); font-weight: 500; transition: var(--transition);
    }
    .side-link:hover { background: var(--hover-bg); color: var(--primary); }
    .side-link.active { background: var(--primary); color: var(--white); font-weight: 600; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
    .side-link.active i { color: var(--white); }
    .side-link i { width: 20px; text-align: center; color: var(--muted); font-size: 16px; transition: .2s; }

    /* Main Content & Navbar */
    .main { margin-left: var(--sidebar-w); flex: 1; min-height: 100vh; display: flex; flex-direction: column; transition: var(--transition); }
    .navbar {
        height: var(--navbar-h); background: var(--white); border-bottom: 1px solid var(--line);
        position: sticky; top: 0; z-index: 200; display: flex; align-items: center; justify-content: space-between; padding: 0 24px;
    }

    .nav-user { display: flex; align-items: center; gap: 12px; }
    .nav-avatar {
        width: 36px; height: 36px; border-radius: 50%; background: var(--primary-light);
        color: var(--primary-dark); display: grid; place-items: center; font-weight: 700;
    }

    .content { padding: 24px; flex: 1; }

    /* Mobile Overlay */
    .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 900; backdrop-filter: blur(2px); }

    @media (max-width: 992px) {
        .sidebar { transform: translateX(-100%); }
        .sidebar.open { transform: translateX(0); }
        .main { margin-left: 0; }
        .overlay.show { display: block; }
    }

    /* Common Utils */
    .btn-logout { background: none; border: none; width: 100%; text-align: left; cursor: pointer; }
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
        <a href="{{ route('petugas.setoran.index') }}" class="side-link {{ request()->routeIs('petugas.setoran.index') ? 'active' : '' }}">
          <i class="fa-solid fa-clipboard-list"></i>
          <span>Daftar Tugas</span>
        </a>
        <a href="{{ route('petugas.map') }}" class="side-link {{ request()->routeIs('petugas.map*') ? 'active' : '' }}">
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
      <button id="sidebarToggle" style="background:none; border:none; font-size:20px; color:var(--muted); cursor:pointer; display:none;">
        <i class="fa-solid fa-bars"></i>
      </button>

      <div style="font-weight:700; color:var(--ink); font-size:1.1rem;">
        @yield('title')
      </div>

      <div class="nav-user">
        <div style="text-align:right; line-height:1.2; display:none; @media(min-width:768px){display:block;}">
            <div style="font-weight:700; font-size:0.9rem;">{{ Auth::user()->name }}</div>
            <div style="font-size:0.75rem; color:var(--muted);">Petugas Lapangan</div>
        </div>
        <div class="nav-avatar">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
      </div>
    </nav>

    <main class="content">
       @yield('content')
    </main>
  </div>
</div>

<script>
    // Simple Sidebar Logic
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    if(window.innerWidth <= 992) toggle.style.display = 'block';

    function toggleMenu() {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    }

    toggle?.addEventListener('click', toggleMenu);
    overlay?.addEventListener('click', toggleMenu);
</script>
@stack('scripts')
</body>
</html>
