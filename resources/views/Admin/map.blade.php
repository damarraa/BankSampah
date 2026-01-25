@extends('layouts.admin') {{-- sesuaikan nama master layout kamu --}}

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

  .wrap{max-width:1100px;margin:0 auto}

  .head{
    display:flex;align-items:flex-start;justify-content:space-between;
    gap:10px;flex-wrap:wrap;margin-bottom:12px
  }
  .title{margin:0;font-size:22px;font-weight:800}
  .sub{margin:6px 0 0;color:var(--muted);font-size:13px}

  .btn{
    padding:10px 14px;border:1px solid var(--line);border-radius:12px;
    text-decoration:none;background: rgba(255,255,255,.04);
    color: var(--text);
    cursor:pointer;font-weight:700;display:inline-flex;align-items:center;gap:8px;
    font-size:13px;
  }
  .btn:hover{filter:brightness(1.06)}

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

  select, input{
    padding:10px 12px;border-radius:12px;border:1px solid var(--line);
    background: rgba(10,15,26,.55);
    color: var(--text);
    font-weight:700;
    outline: none;
  }
  input::placeholder{color: rgba(147,164,199,.8)}
  select:focus, input:focus{
    border-color: rgba(34,197,94,.5);
    box-shadow: 0 0 0 3px rgba(34,197,94,.15);
  }

  #map{height:560px;border:1px solid rgba(255,255,255,.12);border-radius:16px;overflow:hidden}

  .legend{display:flex;gap:10px;flex-wrap:wrap;margin-top:10px}
  .chip{
    padding:8px 10px;border-radius:999px;border:1px solid rgba(255,255,255,.10);
    background: rgba(255,255,255,.03);
    font-weight:700;color: var(--text);
  }
  .dot{width:10px;height:10px;border-radius:999px;display:inline-block;margin-right:8px;vertical-align:middle}
</style>
@endpush

@section('content')
<div class="page-bg">
  <div class="wrap">
    <div class="head">
      <div>
        <h1 class="title">🧑‍💼 Admin — Peta Semua Titik Jemput</h1>
        <p class="sub">Pantau semua titik jemput + posisi petugas realtime.</p>
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a class="btn" href="{{ route('admin.setoran.index') }}">📦 Data Setoran</a>
      </div>
    </div>

    <div class="box">
      <div class="toolbar">
        <div class="muted" id="infoText">Memuat data...</div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
          <input id="qUser" placeholder="Cari nama user..." />
          <select id="statusFilter">
            <option value="__all__">Semua status</option>
            <option value="pending">PENDING</option>
            <option value="diambil">DIAMBIL</option>
            <option value="selesai">SELESAI</option>
            <option value="ditolak">DITOLAK</option>
          </select>
        </div>
      </div>

      <div id="map"></div>

      <div class="legend">
        <div class="chip"><span class="dot" style="background:#3b82f6"></span>Titik Jemput (Orang Lain)</div>
        <div class="chip"><span class="dot" style="background:#22c55e"></span>Titik Jemput (Data Saya)</div>
        <div class="chip"><span class="dot" style="background:#f59e0b"></span>Pending</div>
        <div class="chip"><span class="dot" style="background:#3b82f6"></span>Diambil</div>
        <div class="chip"><span class="dot" style="background:#9ca3af"></span>Selesai</div>
        <div class="chip"><span class="dot" style="background:#ef4444"></span>Ditolak</div>
        <div class="chip">🚛 = posisi petugas realtime</div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
  const dataUrl = "{{ route('admin.map.data') }}";
  const detailUrlBase = "{{ url('/admin/setoran') }}"; // /admin/setoran/{id}

  const map = L.map('map').setView([0.5071, 101.4478], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

  const pickupMarkers = new Map();
  const truckMarkers  = new Map();
  const truckIcon = L.divIcon({ html: "🚛", className: "", iconSize: [28,28], iconAnchor:[14,14] });

  function colorByStatus(status, isMyData){
    const s = String(status||'').toLowerCase();
    if(isMyData){
      if(s === 'pending') return '#10b981';
      if(s === 'diambil') return '#059669';
      if(s === 'selesai') return '#047857';
      if(s === 'ditolak') return '#dc2626';
      return '#22c55e';
    }
    if(s === 'pending') return '#f59e0b';
    if(s === 'diambil') return '#3b82f6';
    if(s === 'selesai') return '#9ca3af';
    if(s === 'ditolak') return '#ef4444';
    return '#3b82f6';
  }

  function circleIcon(color, isMyData){
    const size = isMyData ? 20 : 16;
    const borderWidth = isMyData ? 4 : 3;
    return L.divIcon({
      className: "",
      html: `<div style="
        width:${size}px;height:${size}px;border-radius:999px;background:${color};
        border:${borderWidth}px solid rgba(255,255,255,.95);
        box-shadow:0 10px 24px rgba(0,0,0,.18);
      "></div>`,
      iconSize:[size,size],
      iconAnchor:[size/2,size/2]
    });
  }

  function passFilter(it){
    const f = document.getElementById('statusFilter').value;
    const q = (document.getElementById('qUser').value || '').trim().toLowerCase();

    const okStatus = (f === '__all__') || (String(it.status||'').toLowerCase() === f);
    const okUser = !q || String(it.user_name||'').toLowerCase().includes(q);

    return okStatus && okUser;
  }

  function popupHtml(it){
    const s = String(it.status||'').toUpperCase();
    const addr = it.alamat ? it.alamat : '-';
    const seen = it.petugas_last_seen ? it.petugas_last_seen : '-';
    const petName = it.petugas_name ? it.petugas_name : '-';
    const hasTruck = (it.petugas_lat !== null && it.petugas_lng !== null);
    const isMyData = it.is_my_data || false;
    const dataLabel = isMyData ? '<span style="background:#22c55e;color:#fff;padding:2px 6px;border-radius:6px;font-size:11px;font-weight:700">DATA SAYA</span>' : '';

    return `
      <div style="font-family:Arial;min-width:240px">
        <b>Setoran #${it.id}</b> ${dataLabel}<br>
        <div style="margin-top:6px"><b>User:</b> ${it.user_name ?? '-'}</div>
        <div style="margin-top:6px"><b>Status:</b> ${s}</div>
        <div style="margin-top:6px"><b>Alamat:</b><br>${addr}</div>
        <div style="margin-top:6px"><b>Koordinat:</b> ${it.lat.toFixed(6)}, ${it.lng.toFixed(6)}</div>
        <div style="margin-top:6px"><b>Petugas:</b> ${petName}</div>
        <div style="margin-top:6px"><b>Tracking:</b> ${hasTruck ? '🚛 aktif' : 'Belum ada / belum kirim lokasi'}</div>
        <div style="margin-top:6px"><b>Last seen:</b> ${seen}</div>
        <div style="margin-top:10px">
          <a href="${detailUrlBase}/${it.id}" style="display:inline-block;padding:8px 10px;border:1px solid #333;border-radius:10px;text-decoration:none">Detail</a>
          <a target="_blank" href="https://www.google.com/maps?q=${it.lat},${it.lng}" style="display:inline-block;padding:8px 10px;border:1px solid #0b6b4d;border-radius:10px;text-decoration:none;margin-left:6px">Maps</a>
        </div>
      </div>
    `;
  }

  async function refresh(){
    const res = await fetch(dataUrl, { cache: "no-store" });
    const data = await res.json();
    const items = data.items || [];

    document.getElementById('infoText').textContent =
      `Total titik jemput: ${items.length} • Realtime update`;

    let bounds = [];
    const alivePickup = new Set();
    const aliveTruck = new Set();

    items.forEach(it => {
      alivePickup.add(it.id);

      const ok = passFilter(it);
      const isMyData = it.is_my_data || false;
      const c = colorByStatus(it.status, isMyData);
      const icon = circleIcon(c, isMyData);

      if(!pickupMarkers.has(it.id)){
        const m = L.marker([it.lat, it.lng], { icon }).addTo(map);
        m.bindPopup(popupHtml(it));
        pickupMarkers.set(it.id, m);
      }else{
        pickupMarkers.get(it.id).setLatLng([it.lat, it.lng]);
        pickupMarkers.get(it.id).setIcon(icon);
        pickupMarkers.get(it.id).setPopupContent(popupHtml(it));
      }
      pickupMarkers.get(it.id).setOpacity(ok ? 1 : 0);
      if(ok) bounds.push([it.lat, it.lng]);

      const hasTruck = (it.petugas_lat !== null && it.petugas_lng !== null);
      if(hasTruck){
        aliveTruck.add(it.id);
        if(!truckMarkers.has(it.id)){
          const tm = L.marker([it.petugas_lat, it.petugas_lng], { icon: truckIcon }).addTo(map);
          tm.bindPopup(`<b>🚛 ${it.petugas_name ?? 'Petugas'}</b><br>Setoran #${it.id}<br>Last seen: ${it.petugas_last_seen ?? '-'}`);
          truckMarkers.set(it.id, tm);
        }else{
          truckMarkers.get(it.id).setLatLng([it.petugas_lat, it.petugas_lng]);
          truckMarkers.get(it.id).setPopupContent(`<b>🚛 ${it.petugas_name ?? 'Petugas'}</b><br>Setoran #${it.id}<br>Last seen: ${it.petugas_last_seen ?? '-'}`);
        }
        truckMarkers.get(it.id).setOpacity(ok ? 1 : 0);
        if(ok) bounds.push([it.petugas_lat, it.petugas_lng]);
      }
    });

    for(const [id, m] of pickupMarkers){
      if(!alivePickup.has(id)){
        map.removeLayer(m);
        pickupMarkers.delete(id);
      }
    }
    for(const [id, m] of truckMarkers){
      if(!aliveTruck.has(id)){
        map.removeLayer(m);
        truckMarkers.delete(id);
      }
    }

    if(bounds.length){
      map.fitBounds(L.latLngBounds(bounds).pad(0.2));
    }
  }

  document.getElementById('statusFilter').addEventListener('change', refresh);
  document.getElementById('qUser').addEventListener('input', () => {
    clearTimeout(window.__t);
    window.__t = setTimeout(refresh, 250);
  });

  refresh();
  setInterval(refresh, 1200);
</script>
@endpush
