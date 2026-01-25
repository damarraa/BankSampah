@extends('layouts.petugas') {{-- sesuaikan nama layout petugas kamu --}}

@section('title', 'Peta Semua Titik Jemput')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>

<style>
  :root{
    --bg:#0b1220; --text:#eaf0ff; --muted:#93a4c7;
    --line:rgba(255,255,255,.10);
    --shadow:0 18px 60px rgba(0,0,0,.35);
    --radius:18px;
  }

  .page-bg{
    padding: 18px;
    border-radius: 18px;
    background:
      radial-gradient(1200px 600px at 20% 0%, rgba(34,197,94,.18), transparent 55%),
      radial-gradient(900px 500px at 90% 15%, rgba(59,130,246,.16), transparent 60%),
      var(--bg);
    color: var(--text);
    border: 1px solid rgba(255,255,255,.06);
  }

  .wrap{max-width:1200px;margin:0 auto}
  .head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:12px}
  .title{margin:0;font-size:22px;font-weight:800}
  .sub{margin:6px 0 0;color:var(--muted);font-size:13px}

  .btn{
    padding:10px 14px;border:1px solid var(--line);border-radius:12px;
    text-decoration:none;background: rgba(255,255,255,.04);
    color: var(--text);
    cursor:pointer;font-weight:700;display:inline-flex;align-items:center;gap:8px;
    font-size:13px;
  }
  .btn:hover{filter: brightness(1.06)}

  .box{
    background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
    border:1px solid var(--line);border-radius:var(--radius);
    box-shadow:var(--shadow);
    padding:12px;
  }

  .muted{color:var(--muted);font-size:13px;font-weight:700}

  .toolbar{
    display:flex;gap:10px;flex-wrap:wrap;align-items:center;
    justify-content:space-between;margin-bottom:10px
  }

  select{
    padding:10px 12px;border-radius:12px;border:1px solid var(--line);
    background: rgba(10,15,26,.55);
    color: var(--text);
    font-weight:700;
    outline:none;
  }
  select:focus{
    border-color: rgba(34,197,94,.5);
    box-shadow: 0 0 0 3px rgba(34,197,94,.15);
  }

  #map{height:600px;border:1px solid rgba(255,255,255,.12);border-radius:16px;overflow:hidden}

  .legend{display:flex;gap:10px;flex-wrap:wrap;margin-top:10px}
  .chip{
    padding:8px 10px;border-radius:999px;border:1px solid rgba(255,255,255,.10);
    background: rgba(255,255,255,.03);
    font-weight:700;display:flex;align-items:center;gap:8px;color:var(--text)
  }
  .dot{width:10px;height:10px;border-radius:999px;display:inline-block}
</style>
@endpush

@section('content')
<div class="page-bg">
  <div class="wrap">
    <div class="head">
      <div>
        <h1 class="title">🚛 Petugas — Peta Semua Titik Jemput</h1>
        <p class="sub">Lihat semua titik jemput + statusnya. Yang assigned ke kamu ditandai hijau.</p>
      </div>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a class="btn" href="{{ route('petugas.setoran.index') }}">📦 Daftar Setoran</a>
      </div>
    </div>

    <div class="box">
      <div class="toolbar">
        <div class="muted" id="infoText">Memuat data...</div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
          <span class="muted">Filter status:</span>
          <select id="statusFilter">
            <option value="__all__">Semua</option>
            <option value="pending">PENDING</option>
            <option value="diproses">DIPROSES</option>
            <option value="diambil">DIAMBIL</option>
            <option value="selesai">SELESAI</option>
          </select>
        </div>
      </div>

      <div id="map"></div>

      <div class="legend">
        <div class="chip"><span class="dot" style="background:#22c55e"></span>Titik Jemput (Assigned ke Saya)</div>
        <div class="chip"><span class="dot" style="background:#f59e0b"></span>Pending</div>
        <div class="chip"><span class="dot" style="background:#3b82f6"></span>Diproses/Diambil</div>
        <div class="chip"><span class="dot" style="background:#9ca3af"></span>Selesai</div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
  const dataUrl = "{{ route('petugas.map.data') }}";
  const detailUrlBase = "{{ url('/petugas/setoran') }}";

  const map = L.map('map').setView([0.5071, 101.4478], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

  const pickupMarkers = new Map(); // id -> marker

  function colorByStatus(status, isAssignedToMe){
    const s = String(status||'').toLowerCase();
    if(isAssignedToMe) return '#22c55e';
    if(s === 'pending') return '#f59e0b';
    if(s === 'diproses' || s === 'diambil') return '#3b82f6';
    if(s === 'selesai') return '#9ca3af';
    return '#64748b';
  }

  function circleIcon(color, isAssignedToMe){
    const size = isAssignedToMe ? 20 : 16;
    const borderWidth = isAssignedToMe ? 4 : 3;
    return L.divIcon({
      className: "",
      html: `<div style="width:${size}px;height:${size}px;border-radius:999px;background:${color};
        border:${borderWidth}px solid rgba(255,255,255,.95);box-shadow:0 10px 24px rgba(0,0,0,.18);"></div>`,
      iconSize:[size,size], iconAnchor:[size/2,size/2]
    });
  }

  function shouldShow(status, filter){
    if(filter === '__all__') return true;
    return String(status||'').toLowerCase() === filter;
  }

  function popupHtml(it){
    const s = String(it.status||'').toUpperCase();
    const addr = it.alamat ? it.alamat : '-';
    const userName = it.user_name ? it.user_name : '-';
    const isAssigned = it.is_assigned_to_me || false;
    const assignLabel = isAssigned ? '<span style="background:#22c55e;color:#fff;padding:2px 6px;border-radius:6px;font-size:11px;font-weight:700">ASSIGNED KE SAYA</span>' : '';

    return `
      <div style="font-family:Arial;min-width:240px">
        <b>Setoran #${it.id}</b> ${assignLabel}<br>
        <div style="margin-top:6px"><b>User:</b> ${userName}</div>
        <div style="margin-top:6px"><b>Status:</b> ${s}</div>
        <div style="margin-top:6px"><b>Alamat:</b><br>${addr}</div>
        <div style="margin-top:6px"><b>Koordinat:</b> ${it.lat.toFixed(6)}, ${it.lng.toFixed(6)}</div>
        <div style="margin-top:10px">
          <a href="${detailUrlBase}/${it.id}" style="display:inline-block;padding:8px 10px;border:1px solid #333;border-radius:10px;text-decoration:none">Detail</a>
          <a target="_blank" href="https://www.google.com/maps?q=${it.lat},${it.lng}" style="display:inline-block;padding:8px 10px;border:1px solid #0b6b4d;border-radius:10px;text-decoration:none;margin-left:6px">Maps</a>
        </div>
      </div>
    `;
  }

  async function refresh(){
    const filter = document.getElementById('statusFilter').value;
    const res = await fetch(dataUrl, { cache: "no-store" });
    const data = await res.json();
    const items = data.items || [];

    document.getElementById('infoText').textContent =
      `Total titik jemput: ${items.length} • Realtime update`;

    let bounds = [];
    const alivePickup = new Set();

    for(const it of items){
      const ok = shouldShow(it.status, filter);
      alivePickup.add(it.id);

      const isAssigned = it.is_assigned_to_me || false;
      const icon = circleIcon(colorByStatus(it.status, isAssigned), isAssigned);

      if(!pickupMarkers.has(it.id)){
        const m = L.marker([it.lat, it.lng], { icon }).addTo(map);
        pickupMarkers.set(it.id, m);
      }else{
        pickupMarkers.get(it.id).setLatLng([it.lat, it.lng]);
        pickupMarkers.get(it.id).setIcon(icon);
      }

      pickupMarkers.get(it.id).bindPopup(popupHtml(it));
      pickupMarkers.get(it.id).setOpacity(ok ? 1 : 0);
      if(ok) bounds.push([it.lat, it.lng]);
    }

    for(const [id, m] of pickupMarkers){
      if(!alivePickup.has(id)){
        map.removeLayer(m);
        pickupMarkers.delete(id);
      }
    }

    if(bounds.length){
      map.fitBounds(L.latLngBounds(bounds).pad(0.2));
    }
  }

  document.getElementById('statusFilter').addEventListener('change', refresh);

  refresh();
  setInterval(refresh, 2000);
</script>
@endpush
