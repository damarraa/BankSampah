<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'User') - {{ config('app.name', 'Bank Sampah') }}</title>

  <style>
    :root{
      --g1:#064e3b;
      --g2:#16a34a;
      --g3:#22c55e;
      --b1:#38bdf8;

      --bg:#f3fff7;
      --card: rgba(255,255,255,.86);
      --text:#0f172a;
      --muted:#64748b;
      --line: rgba(2,44,24,.12);

      --shadow: 0 18px 48px rgba(2, 44, 24, .12);
      --shadow2: 0 28px 80px rgba(2, 44, 24, .18);
      --radius: 22px;

      /* ganti gambar banner kalau perlu */
      --banner-url: url('/images/banner.jpg');
    }

    *{box-sizing:border-box}
    body{
      margin:0;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
      color:var(--text);
      background:
        radial-gradient(900px 380px at 15% -10%, rgba(34,197,94,.22), transparent 60%),
        radial-gradient(820px 360px at 95% 10%, rgba(56,189,248,.14), transparent 55%),
        radial-gradient(900px 520px at 50% 120%, rgba(134,239,172,.55), transparent 55%),
        linear-gradient(180deg, #ffffff 0%, var(--bg) 72%);
      overflow-x:hidden;
    }

    /* HEADER */
    .header{
      position:sticky; top:0; z-index:20;
      backdrop-filter: blur(12px);
      background: rgba(243,255,247,.72);
      border-bottom: 1px solid rgba(34,197,94,.14);
    }
    .header-inner{
      max-width:1180px;margin:0 auto;padding:12px 16px;
      display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;
    }
    .brand{display:flex;align-items:center;gap:12px}
    .mark{
      width:44px;height:44px;border-radius:18px;position:relative;overflow:hidden;
      background: linear-gradient(135deg, var(--g3), var(--g1));
      box-shadow: 0 18px 50px rgba(34,197,94,.22);
      flex: 0 0 auto;
    }
    .brand h1{margin:0;font-size:16.5px;font-weight:1000;line-height:1.1}
    .brand .sub{margin-top:3px;color:var(--muted);font-size:12.3px;font-weight:750}

    .nav{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
    .btn{
      border:1px solid rgba(34,197,94,.18);
      background: rgba(255,255,255,.88);
      color:var(--text);
      padding:10px 14px;border-radius:16px;
      text-decoration:none;font-weight:950;font-size:13.2px;
      display:inline-flex;align-items:center;gap:10px;
      transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
      box-shadow: 0 2px 0 rgba(2,44,24,.03);
      white-space:nowrap;
    }
    .btn:hover{transform:translateY(-2px);box-shadow:var(--shadow)}
    .btn-primary{
      background: linear-gradient(135deg, rgba(34,197,94,.98), rgba(6,78,59,.98));
      color:#fff;border-color:transparent;
      box-shadow: 0 22px 60px rgba(34,197,94,.22);
    }
    .btn-danger{
      border-color: rgba(239,68,68,.35);
      background: rgba(239,68,68,.10);
      color:#991b1b;
    }
    .active{
      background: rgba(34,197,94,.14);
      border-color: rgba(34,197,94,.30);
    }

    /* CONTENT WRAPPER */
    .wrap{max-width:1180px;margin:0 auto;padding:14px 16px 26px;}
    .content{max-width:1180px;margin:0 auto;padding:16px 0 0;}

    /* FLASH */
    .flash{
      border-radius: 16px;
      padding: 12px 14px;
      margin: 0 0 14px;
      border: 1px solid rgba(34,197,94,.20);
      background: rgba(34,197,94,.10);
      font-weight: 850;
    }
    .flash.error{
      border-color: rgba(239,68,68,.25);
      background: rgba(239,68,68,.10);
    }

    /* FOOTER */
    .footer{margin-top:26px;border-top:1px solid rgba(34,197,94,.12);background:rgba(255,255,255,.64)}
    .footer-inner{
      max-width:1180px;margin:0 auto;padding:18px 16px;
      display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;
      color:var(--muted);font-weight:800;font-size:13px
    }
    .footer a{color: var(--g1); font-weight:1000; text-decoration:none}
    .footer a:hover{text-decoration:underline}

    /* Responsive small */
    @media (max-width:520px){
      .btn{padding:10px 12px}
      .brand .sub{display:none}
    }
  </style>

  @stack('styles')
</head>

<body>
  <header class="header">
    <div class="header-inner">
      <div class="brand">
        <div class="mark"></div>
        <div>
          <h1>Bank Sampah</h1>
          <div class="sub">Cinta lingkungan • Setor jadi mudah 🌿</div>
        </div>
      </div>

      <nav class="nav">
        <a class="btn {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">🏠 Dashboard</a>
        <a class="btn {{ request()->routeIs('user.setoran.index') ? 'active' : '' }}" href="{{ route('user.setoran.index') }}">📦 Riwayat</a>
        <a class="btn btn-primary {{ request()->routeIs('user.setoran.create') ? 'active' : '' }}" href="{{ route('user.setoran.create') }}">➕ Buat Setoran</a>
        <a class="btn {{ request()->routeIs('user.map*') ? 'active' : '' }}" href="{{ route('user.map') }}">🗺️ Peta</a>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
          @csrf
          <button type="submit" class="btn btn-danger">Logout</button>
        </form>
      </nav>
    </div>
  </header>

  <div class="wrap">
    <main class="content">

      @if(session('success'))
        <div class="flash">{{ session('success') }}</div>
      @endif

      @if(session('error'))
        <div class="flash error">{{ session('error') }}</div>
      @endif

      @yield('content')
    </main>
  </div>

  <footer class="footer">
    <div class="footer-inner">
      <div>© {{ date('Y') }} Bank Sampah • Cinta Lingkungan 🌿</div>
      <div>
        <a href="{{ route('user.setoran.create') }}">Buat Setoran</a> •
        <a href="{{ route('user.setoran.index') }}">Riwayat</a>
      </div>
    </div>
  </footer>

  @stack('scripts')
</body>
</html>
