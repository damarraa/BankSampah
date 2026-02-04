@extends('layouts.user')
@section('title', 'Dashboard')

@php
  use Illuminate\Support\Str;

  $groups = $kategori->groupBy(function($k){
    return $k->masterKategori?->nama_kategori ?? 'Lainnya';
  });

  $totalCount = $kategori->count();
  $totalSetoran = (int) $setoranPerTahun->sum('total'); // Total transaksi tahun ini

  // Total Pendapatan (jika ada filter tahun, pakai data filter, jika tidak total semua)
  $totalPendapatanDisplay = $tahun
    ? optional($pendapatanPerTahun->firstWhere('tahun', $tahun))->total
    : $totalPendapatan;
@endphp

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />

<style>
  :root{
    --brand: #10b981;
    --brand-dark: #059669;
    --brand-soft: #ecfdf5;
    --bg: #f8fafc;
    --card: #ffffff;
    --ink: #0f172a;
    --muted: #64748b;
    --line: #e2e8f0;
    --radius: 20px;
  }

  body, .dashboard-container { font-family: "Plus Jakarta Sans", sans-serif; background-color: var(--bg); }

  .container-fluid { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 16px; }
  @media (min-width: 768px) { .container-fluid { padding: 0 32px; } }

  /* ===== HERO HEADER (CONSISTENT STYLE) ===== */
  .dashboard-header {
    background: linear-gradient(135deg, #10b981 0%, #047857 100%);
    padding: 40px 0 90px; /* Extra padding bawah untuk overlap */
    color: #fff;
    border-radius: 0 0 50px 50px;
    box-shadow: 0 10px 30px -10px rgba(16, 185, 129, 0.5);
    margin-bottom: -60px;
    position: relative; overflow: hidden; z-index: 1;
  }

  /* Dekorasi Header */
  .dashboard-header::before, .dashboard-header::after {
    content: ""; position: absolute; border-radius: 50%;
    background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); pointer-events: none;
  }
  .dashboard-header::before { width: 300px; height: 300px; top: -100px; left: -50px; }
  .dashboard-header::after { width: 200px; height: 200px; bottom: -50px; right: -20px; opacity: 0.6; }

  .hero-content { position: relative; z-index: 2; text-align: center; }
  .welcome-title { font-size: 1.8rem; font-weight: 800; margin: 0 0 8px; letter-spacing: -0.5px; }
  .welcome-subtitle { font-size: 1rem; opacity: 0.95; font-weight: 500; max-width: 600px; margin: 0 auto; line-height: 1.6; }

  /* ===== STATS SECTION (OVERLAP) ===== */
  .stats-grid {
    display: grid; grid-template-columns: repeat(1, 1fr); gap: 16px;
    position: relative; z-index: 10; margin-bottom: 30px;
  }
  @media (min-width: 640px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }

  .stat-card {
    background: #fff; border-radius: var(--radius); padding: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid rgba(255,255,255,0.8);
    display: flex; align-items: center; gap: 16px; transition: transform 0.2s;
  }
  .stat-card:hover { transform: translateY(-3px); }

  .stat-icon {
    width: 56px; height: 56px; border-radius: 16px;
    display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
    flex-shrink: 0;
  }
  .icon-green { background: #ecfdf5; color: #10b981; }
  .icon-blue { background: #eff6ff; color: #3b82f6; }
  .icon-amber { background: #fffbeb; color: #f59e0b; }

  .stat-info h4 { margin: 0 0 4px; font-size: 0.85rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; }
  .stat-info .value { font-size: 1.4rem; font-weight: 900; color: var(--ink); line-height: 1; }
  .stat-info .sub { font-size: 0.75rem; color: var(--muted); font-weight: 600; margin-top: 4px; }

  /* ===== FILTERS & CATALOG ===== */
  .section-header {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;
  }
  .section-title { font-size: 1.25rem; font-weight: 800; color: var(--ink); display: flex; align-items: center; gap: 8px; }

  /* Scrollable Filter Tabs */
  .filter-wrapper {
    display: flex; gap: 8px; overflow-x: auto; padding-bottom: 5px;
    -webkit-overflow-scrolling: touch; scrollbar-width: none; /* Hide scrollbar Firefox */
  }
  .filter-wrapper::-webkit-scrollbar { display: none; /* Hide scrollbar Chrome */ }

  .filter-btn {
    padding: 8px 16px; background: #fff; border: 1px solid var(--line); border-radius: 50px;
    font-size: 0.85rem; font-weight: 700; color: var(--muted); white-space: nowrap; cursor: pointer; transition: 0.2s;
  }
  .filter-btn:hover { border-color: var(--brand); color: var(--brand-dark); }
  .filter-btn.active { background: var(--brand); border-color: var(--brand); color: #fff; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }
  .filter-count {
    font-size: 0.7rem; background: rgba(0,0,0,0.1); padding: 2px 6px; border-radius: 10px; margin-left: 6px;
  }
  .filter-btn.active .filter-count { background: rgba(255,255,255,0.25); color: #fff; }

  /* ===== PRODUCT GRID ===== */
  .products-grid {
    display: grid; grid-template-columns: repeat(1, 1fr); gap: 20px; padding-bottom: 60px;
  }
  @media (min-width: 640px) { .products-grid { grid-template-columns: repeat(2, 1fr); } }
  @media (min-width: 900px) { .products-grid { grid-template-columns: repeat(3, 1fr); } }
  @media (min-width: 1200px) { .products-grid { grid-template-columns: repeat(4, 1fr); } }

  .product-card {
    background: #fff; border: 1px solid var(--line); border-radius: 16px; overflow: hidden;
    transition: 0.2s; display: flex; flex-direction: column; height: 100%;
  }
  .product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.08); border-color: var(--brand-soft); }

  .product-img-wrap {
    position: relative; width: 100%; aspect-ratio: 4/3; background: #f1f5f9; overflow: hidden;
  }
  .product-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
  .product-card:hover .product-img-wrap img { transform: scale(1.05); }

  .badge-cat {
    position: absolute; top: 10px; left: 10px;
    background: rgba(255,255,255,0.95); backdrop-filter: blur(4px);
    padding: 4px 10px; border-radius: 8px;
    font-size: 0.7rem; font-weight: 800; color: var(--ink); box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  .product-body { padding: 16px; flex: 1; display: flex; flex-direction: column; }
  .p-title { font-size: 1rem; font-weight: 800; color: var(--ink); margin: 0 0 6px; line-height: 1.3; }
  .p-desc { font-size: 0.8rem; color: var(--muted); margin-bottom: 12px; line-height: 1.5; flex: 1; }

  .p-price-row { display: flex; justify-content: space-between; align-items: flex-end; margin-top: auto; }
  .p-label { font-size: 0.7rem; font-weight: 700; color: var(--muted); display: block; margin-bottom: 2px; }
  .p-value { font-size: 1.1rem; font-weight: 900; color: var(--brand-dark); }
  .p-unit { font-size: 0.8rem; color: var(--muted); font-weight: 600; }

  .btn-add {
    margin-top: 14px; width: 100%; padding: 10px; border-radius: 10px; border: none;
    background: var(--brand-soft); color: var(--brand-dark); font-weight: 800; font-size: 0.85rem;
    cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;
  }
  .btn-add:hover { background: var(--brand); color: #fff; }

  /* Empty State */
  .empty-state { grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #fff; border-radius: 16px; border: 1px dashed var(--line); }
  .empty-icon { font-size: 3rem; color: var(--line); margin-bottom: 15px; }
</style>
@endpush

@section('content')
<div class="dashboard-container">

  <div class="dashboard-header">
    <div class="container-fluid">
      <div class="hero-content">
        <h1 class="welcome-title">👋 Halo, {{ Auth::user()->name ?? 'User' }}!</h1>
        <p class="welcome-subtitle">
          Selamat datang di dashboard SampahKu. Kelola sampah, pantau pendapatan, dan jaga lingkungan bersama-sama.
        </p>
      </div>
    </div>
  </div>

  <div class="container-fluid">

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fa-solid fa-wallet"></i></div>
        <div class="stat-info">
          <h4>Pendapatan Anda</h4>
          <div class="value">Rp {{ number_format($totalPendapatanDisplay, 0, ',', '.') }}</div>
          <div class="sub">
            @if($tahun) Tahun {{ $tahun }} @else Semua Waktu @endif
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fa-solid fa-receipt"></i></div>
        <div class="stat-info">
          <h4>Total Transaksi</h4>
          <div class="value">{{ number_format($totalSetoran) }}</div>
          <div class="sub">Kali setoran berhasil</div>
        </div>
      </div>

      <div class="stat-card" style="background: #f8fafc; border: 2px dashed #cbd5e1; justify-content: center;">
        <form action="{{ route('user.dashboard') }}" method="GET" style="width: 100%;">
          <div style="display:flex; gap:10px; align-items:center; justify-content: space-between;">
            <div style="font-weight:700; color:var(--muted); font-size:0.9rem;">
              <i class="fa-solid fa-calendar-days"></i> Filter Tahun
            </div>
            <select name="tahun" onchange="this.form.submit()"
              style="width:auto; padding: 8px 12px; border-radius: 8px; font-size:0.85rem; border-color:#cbd5e1;">
              <option value="">Semua</option>
              @foreach($listTahun as $t)
                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
              @endforeach
            </select>
          </div>
        </form>
      </div>
    </div>

    <div class="section-header">
      <div class="section-title">
        <i class="fa-solid fa-recycle" style="color:var(--brand)"></i> Katalog Sampah
      </div>

      <div class="filter-wrapper" id="filterTabs">
        <button class="filter-btn active" data-filter="__all__">
          Semua <span class="filter-count">{{ $totalCount }}</span>
        </button>
        @foreach($groups as $gName => $list)
          @php $gKey = Str::slug($gName); @endphp
          <button class="filter-btn" data-filter="{{ $gKey }}">
            {{ $gName }} <span class="filter-count">{{ $list->count() }}</span>
          </button>
        @endforeach
      </div>
    </div>

    <div class="products-grid" id="productsGrid">
      @forelse($kategori as $k)
        @php
          $gName = $k->masterKategori?->nama_kategori ?? 'Lainnya';
          $gKey  = Str::slug($gName);
        @endphp

        <div class="product-card" data-group="{{ $gKey }}">
          <div class="product-img-wrap">
            <span class="badge-cat">{{ $gName }}</span>
            @if(!empty($k->gambar_sampah))
              <img src="{{ asset('storage/'.$k->gambar_sampah) }}" alt="{{ $k->nama_sampah }}">
            @else
              <div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--muted); font-weight:700;">
                <i class="fa-regular fa-image" style="font-size:2rem; margin-right:8px;"></i> No Image
              </div>
            @endif
          </div>

          <div class="product-body">
            <h3 class="p-title">{{ $k->nama_sampah }}</h3>
            <p class="p-desc">
              {{ $k->deskripsi ? Str::limit($k->deskripsi, 60) : 'Tidak ada deskripsi tersedia.' }}
            </p>

            <div class="p-price-row">
              <div>
                <span class="p-label">Estimasi Harga</span>
                <span class="p-value">
                  {{ $k->harga_satuan ? 'Rp ' . number_format($k->harga_satuan, 0, ',', '.') : 'Gratis' }}
                </span>
                <span class="p-unit">/ {{ $k->jenis_satuan ?? 'kg' }}</span>
              </div>
            </div>

            <a href="{{ route('user.setoran.create', ['kategori_sampah_id' => $k->id]) }}" class="btn-add">
              <i class="fa-solid fa-plus"></i> Tambah ke Setoran
            </a>
          </div>
        </div>
      @empty
        <div class="empty-state">
          <div class="empty-icon"><i class="fa-solid fa-box-open"></i></div>
          <h3 style="font-weight:800; color:var(--ink); margin-bottom:8px;">Belum ada data sampah</h3>
          <p style="color:var(--muted);">Admin belum menambahkan kategori sampah saat ini.</p>
        </div>
      @endforelse
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.product-card');

    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        // Active State
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        const filterVal = tab.dataset.filter;

        // Filter Logic
        let count = 0;
        cards.forEach(card => {
          if(filterVal === '__all__' || card.dataset.group === filterVal){
            card.style.display = 'flex';
            count++;
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  });
</script>
@endpush
