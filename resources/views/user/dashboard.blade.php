@extends('layouts.user')

@section('title', 'Dashboard')

@php
  use Illuminate\Support\Str;

  $groups = $kategori->groupBy(function($k){
    return $k->masterKategori?->nama_kategori ?? 'Lainnya';
  });

  $totalCount = $kategori->count();
  $featured = $kategori->take(6);
@endphp

@push('styles')
<style>
  /* ==== DASHBOARD-ONLY STYLES ==== */
  .banner{
    border-radius: 30px; overflow:hidden;
    border: 1px solid rgba(34,197,94,.18);
    box-shadow: var(--shadow2);
    position:relative;
    background:
      linear-gradient(90deg, rgba(6,78,59,.56), rgba(6,78,59,.10)),
      var(--banner-url);
    background-size: cover;
    background-position:center;
    min-height: 270px;
  }
  .banner-inner{
    position:relative; z-index:2;
    padding: 18px;
    height:100%;
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:14px;
    flex-wrap:wrap;
  }
  .glass{
    background: rgba(255,255,255,.16);
    border: 1px solid rgba(255,255,255,.28);
    border-radius: 22px;
    padding: 12px 14px;
    backdrop-filter: blur(12px);
    box-shadow: 0 18px 50px rgba(0,0,0,.12);
    max-width: 680px;
  }
  .glass .t{margin:0;font-size:16px;font-weight:1000;color:#fff}
  .glass .s{margin:6px 0 0;font-size:13.2px;color:rgba(255,255,255,.92);font-weight:750;line-height:1.5}
  .chips{display:flex;gap:10px;flex-wrap:wrap}
  .chip{
    padding: 10px 12px;border-radius:999px;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.28);
    color:#fff;font-weight:1000;font-size:12.8px;
    backdrop-filter: blur(12px);
    box-shadow: 0 14px 36px rgba(0,0,0,.12);
  }

  .stats{
    display:grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin: 14px 0 10px;
  }
  @media (max-width: 860px){ .stats{grid-template-columns:1fr} }

  .stat{
    background: var(--card);
    border: 1px solid rgba(34,197,94,.14);
    border-radius: 22px;
    box-shadow: 0 16px 48px rgba(2,44,24,.08);
    padding: 14px;
  }
  .stat .k{color:var(--muted);font-size:12.8px;font-weight:900}
  .stat .v{margin-top:6px;font-weight:1000;font-size:20px}
  .badge{
    display:inline-flex;align-items:center;gap:8px;
    padding:6px 10px;border-radius:999px;
    border:1px solid rgba(34,197,94,.18);
    background: rgba(34,197,94,.10);
    color: var(--g1);
    font-weight:1000;font-size:12px;
    margin-top:10px;
  }
  .yrow{
    margin-top:10px;
    padding:10px 12px;
    border-radius:16px;
    border:1px solid rgba(2,44,24,.10);
    background: rgba(255,255,255,.80);
    display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;
    font-weight:900;
  }
  select{
    width:100%;
    padding:10px 12px;
    border-radius: 14px;
    border: 1px solid rgba(2,44,24,.14);
    background: rgba(255,255,255,.90);
    font-weight:900;
    outline:none;
  }

  .controls{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin: 8px 0 10px;}
  .search{
    flex: 1 1 380px;
    display:flex;gap:10px;align-items:center;
    padding: 12px 14px;
    border-radius: 18px;
    background: rgba(255,255,255,.90);
    border:1px solid rgba(34,197,94,.14);
    box-shadow: 0 16px 44px rgba(2,44,24,.08);
  }
  .search input{width:100%;border:none;outline:none;background:transparent;font-size:13.8px;font-weight:850}
  .info{color:var(--muted);font-size:13px;font-weight:850}

  .bubbles{
    display:flex; gap:10px; flex-wrap:wrap; justify-content:center;
    padding: 12px 14px;
    border-radius: 999px;
    background: rgba(255,255,255,.88);
    border:1px solid rgba(34,197,94,.14);
    box-shadow: 0 18px 50px rgba(2,44,24,.10);
    backdrop-filter: blur(12px);
    margin: 10px 0 16px;
  }
  .bubble{
    border:1px solid rgba(34,197,94,.16);
    background: rgba(34,197,94,.10);
    padding: 10px 14px;border-radius: 999px;
    font-weight:1000;font-size:12.8px;cursor:pointer;
    display:flex;gap:8px;align-items:center;
  }
  .bubble.active{background: linear-gradient(135deg, rgba(34,197,94,.98), rgba(6,78,59,.98));border-color:transparent;color:#fff}
  .count{font-size:12px;font-weight:1000;padding:4px 8px;border-radius:999px;background: rgba(255,255,255,.72);border:1px solid rgba(255,255,255,.60)}

  .section-head{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;flex-wrap:wrap;margin: 16px 0 10px;}
  .section-head h3{margin:0;font-size:15.5px;font-weight:1000}
  .section-head p{margin:0;color:var(--muted);font-size:13px;font-weight:750}

  .grid{
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 14px;
  }
  .card{
    background: var(--card);
    border: 1px solid rgba(34,197,94,.14);
    border-radius: var(--radius);
    padding: 14px;
    box-shadow: 0 16px 48px rgba(2,44,24,.09);
    min-height: 238px;
    display:flex;flex-direction:column;justify-content:space-between;
  }
  .media{
    width:100%;height:150px;border-radius:18px;
    border: 1px solid rgba(34,197,94,.14);
    background: rgba(255,255,255,.92);
    overflow:hidden;
    display:flex;align-items:center;justify-content:center;
    margin-bottom: 12px;
  }
  .media img{width:100%;height:100%;object-fit:contain;padding:10px}
  .fallback{font-weight:1000;color: rgba(6,48,35,.75);font-size:13px}
  .title{margin:0 0 4px;font-size:16px;font-weight:1000}
  .pill{font-size:12px;padding:6px 10px;border-radius:999px;border:1px solid rgba(34,197,94,.16);background: rgba(34,197,94,.10);font-weight:1000;display:inline-flex}
  .desc{margin-top:8px;color:var(--muted);font-size:13.2px;line-height:1.55}
  .bottom{margin-top:12px;padding-top:10px;border-top:1px dashed rgba(2,44,24,.14);display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:flex-end}
  .price b{font-size:17px;font-weight:1000;color:var(--g1)}
  .btn-add{
    padding:10px 12px;border-radius:16px;
    border:1px solid rgba(34,197,94,.22);
    background: rgba(34,197,94,.14);
    color: var(--g1);
    font-weight:1000;font-size:13.2px;text-decoration:none;
    display:inline-flex;align-items:center;gap:8px;
  }

  .group-title{display:flex;align-items:center;justify-content:space-between;margin:18px 0 10px;gap:12px;flex-wrap:wrap}
  .tag{font-size:12px;color: var(--g1);background: rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.18);padding:7px 12px;border-radius:999px;font-weight:1000}
</style>
@endpush

@section('content')

<section class="banner">
  <div class="banner-inner">
    <div class="glass">
      <p class="t">Setor Sampah Lebih Mudah 🌱</p>
      <p class="s">
        Katalog item dikelompokkan dari <b>Master Kategori</b>.
        Kamu juga bisa lihat pendapatan dari setoran yang sudah <b>SELESAI</b>.
      </p>
    </div>

    <div class="chips">
      <div class="chip">Total Item: <b>{{ $totalCount }}</b></div>
      <div class="chip">Kategori: <b>{{ $groups->count() }}</b></div>
    </div>
  </div>
</section>

<section class="stats">
  <div class="stat">
    <div class="k">Total Pendapatan (Status: selesai)</div>
    <div class="v">Rp {{ number_format($totalPendapatan,0,',','.') }}</div>
    <div class="badge">{{ $tahun ? 'Filter Tahun: '.$tahun : 'Semua Tahun' }}</div>
  </div>

  <div class="stat">
    <div class="k">Filter Pendapatan per Tahun</div>
    <form method="GET" action="{{ route('user.dashboard') }}">
      <select name="tahun" onchange="this.form.submit()">
        <option value="">Semua Tahun</option>
        @foreach($daftarTahun as $t)
          <option value="{{ $t }}" {{ (string)$tahun === (string)$t ? 'selected' : '' }}>{{ $t }}</option>
        @endforeach
      </select>
    </form>
    <div class="badge">Hanya setoran selesai</div>
  </div>

  <div class="stat">
    <div class="k">Pendapatan per Tahun (Selesai)</div>
    @forelse($pendapatanPerTahun as $p)
      <div class="yrow">
        <span>{{ $p->tahun }}</span>
        <span>Rp {{ number_format((int)$p->total,0,',','.') }}</span>
      </div>
    @empty
      <div class="yrow">Belum ada data pendapatan</div>
    @endforelse
  </div>
</section>

<section class="stats" style="margin-top:0">
  <div class="stat" style="grid-column:1/-1">
    <div class="k">Jumlah Setoran per Tahun (Semua Status)</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:10px;margin-top:10px">
      @forelse($setoranPerTahun as $y)
        <div class="yrow" style="margin:0">
          <span>{{ $y->tahun }}</span>
          <span>{{ number_format((int)$y->total,0,',','.') }} setoran</span>
        </div>
      @empty
        <div class="yrow">Belum ada setoran</div>
      @endforelse
    </div>
  </div>
</section>

<section class="controls">
  <div class="search">
    <span>🔎</span>
    <input id="searchInput" type="text" placeholder="Cari nama sampah atau kategori...">
  </div>
  <div class="info" id="resultInfo">Menampilkan: {{ $totalCount }} item</div>
</section>

<section class="bubbles" id="bubbles">
  <button class="bubble active" type="button" data-filter="__all__">
    All <span class="count">{{ $totalCount }}</span>
  </button>

  @foreach($groups as $gName => $list)
    <button class="bubble" type="button" data-filter="{{ Str::slug($gName) }}">
      {{ $gName }} <span class="count">{{ $list->count() }}</span>
    </button>
  @endforeach
</section>

<section class="section-head">
  <div>
    <h3>Rekomendasi</h3>
    <p>Item untuk cepat ditambahkan.</p>
  </div>
</section>

<section class="grid">
  @forelse($featured as $k)
    @php
      $gName = $k->masterKategori?->nama_kategori ?? 'Lainnya';
      $gKey  = Str::slug($gName);
    @endphp

    <article class="card product-card"
      data-name="{{ Str::lower($k->nama_sampah) }}"
      data-cat="{{ Str::lower($gName) }}"
      data-group="{{ $gKey }}">

      <div class="media">
        @if(!empty($k->gambar_sampah))
          <img src="{{ asset('storage/'.$k->gambar_sampah) }}" alt="Gambar {{ $k->nama_sampah }}">
        @else
          <div class="fallback">🌿 Tidak ada gambar</div>
        @endif
      </div>

      <div>
        <div class="title">{{ $k->nama_sampah }}</div>
        <span class="pill">{{ $gName }}</span>
        <div class="desc">{{ $k->deskripsi ?: 'Tidak ada deskripsi.' }}</div>
      </div>

      <div class="bottom">
        <div class="price">
          <span>Harga </span>
          <b>{{ $k->harga_satuan !== null ? 'Rp ' . number_format($k->harga_satuan, 0, ',', '.') : '-' }}</b>
          <span>/ {{ $k->jenis_satuan ?? '-' }}</span>
        </div>

        <a class="btn-add" href="{{ route('user.setoran.create', ['kategori_sampah_id' => $k->id]) }}">
          Tambah +
        </a>
      </div>
    </article>
  @empty
    <div class="card">Belum ada data.</div>
  @endforelse
</section>

<section class="section-head" style="margin-top:18px">
  <div>
    <h3>Semua Jenis Sampah (per Master Kategori)</h3>
    <p>Gunakan bubble atau search.</p>
  </div>
</section>

<section id="groupContainer">
  @forelse($groups as $gName => $list)
    @php $gKey = Str::slug($gName); @endphp

    <div class="group-block" data-group="{{ $gKey }}">
      <div class="group-title">
        <div><b>🌿 {{ $gName }}</b></div>
        <div class="tag">{{ $list->count() }} item</div>
      </div>

      <div class="grid">
        @foreach($list as $k)
          <article class="card product-card"
            data-name="{{ Str::lower($k->nama_sampah) }}"
            data-cat="{{ Str::lower($gName) }}"
            data-group="{{ $gKey }}">

            <div class="media">
              @if(!empty($k->gambar_sampah))
                <img src="{{ asset('storage/'.$k->gambar_sampah) }}" alt="Gambar {{ $k->nama_sampah }}">
              @else
                <div class="fallback">🌿 Tidak ada gambar</div>
              @endif
            </div>

            <div>
              <div class="title">{{ $k->nama_sampah }}</div>
              <span class="pill">{{ $gName }}</span>
              <div class="desc">{{ $k->deskripsi ?: 'Tidak ada deskripsi.' }}</div>
            </div>

            <div class="bottom">
              <div class="price">
                <span>Harga </span>
                <b>{{ $k->harga_satuan !== null ? 'Rp ' . number_format($k->harga_satuan, 0, ',', '.') : '-' }}</b>
                <span>/ {{ $k->jenis_satuan ?? '-' }}</span>
              </div>

              <a class="btn-add" href="{{ route('user.setoran.create', ['kategori_sampah_id' => $k->id]) }}">
                Tambah +
              </a>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  @empty
    <div class="card">Belum ada data sampah dari admin.</div>
  @endforelse
</section>

@endsection

@push('scripts')
<script>
  // Bubble filter + search realtime
  (function(){
    const bubbles = document.getElementById('bubbles');
    const groups = Array.from(document.querySelectorAll('.group-block'));
    const cards = Array.from(document.querySelectorAll('.product-card'));
    const searchInput = document.getElementById('searchInput');
    const resultInfo = document.getElementById('resultInfo');

    let activeFilter = '__all__';
    let q = '';

    function apply(){
      const query = (q || '').trim().toLowerCase();
      let visibleCount = 0;

      cards.forEach((c)=>{
        const name = (c.dataset.name || '');
        const cat  = (c.dataset.cat || '');
        const g    = (c.dataset.group || '');

        const matchGroup = (activeFilter === '__all__') || (g === activeFilter);
        const matchQuery = !query || name.includes(query) || cat.includes(query);

        const show = matchGroup && matchQuery;
        c.style.display = show ? '' : 'none';
        if(show) visibleCount++;
      });

      groups.forEach(gb=>{
        const key = gb.dataset.group;
        const hasVisible = gb.querySelectorAll('.product-card:not([style*="display: none"])').length > 0;
        const groupMatch = (activeFilter === '__all__') || (key === activeFilter);
        gb.style.display = (groupMatch && hasVisible) ? '' : 'none';
      });

      if(resultInfo) resultInfo.textContent = `Menampilkan: ${visibleCount} item`;
    }

    if(bubbles){
      bubbles.addEventListener('click', (e)=>{
        const btn = e.target.closest('.bubble');
        if(!btn) return;
        activeFilter = btn.dataset.filter;

        Array.from(bubbles.querySelectorAll('.bubble')).forEach(b=>{
          b.classList.toggle('active', b.dataset.filter === activeFilter);
        });

        apply();
      });
    }

    if(searchInput){
      searchInput.addEventListener('input', ()=>{
        q = searchInput.value;
        apply();
      });
    }

    apply();
  })();
</script>
@endpush
