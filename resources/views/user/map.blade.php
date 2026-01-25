@extends('layouts.user')

@section('title', 'Peta Titik Jemput Saya')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
  .page-title{
    display:flex;align-items:flex-end;justify-content:space-between;gap:12px;flex-wrap:wrap;
    margin: 6px 0 14px;
  }
  .page-title h2{margin:0;font-size:18px;font-weight:1000}
  .page-title p{margin:6px 0 0;color:var(--muted);font-weight:800;font-size:13px}

  .box{
    background: var(--card);
    border: 1px solid rgba(34,197,94,.14);
    border-radius: 22px;
    box-shadow: 0 16px 48px rgba(2,44,24,.08);
    padding: 14px;
  }

  .toolbar{
    display:flex;gap:12px;flex-wrap:wrap;align-items:center;justify-content:space-between;
    margin-bottom: 12px;
  }
  .muted{color:var(--muted);font-size:13px;font-weight:800}

  select{
    padding:10px 12px;border-radius:14px;
    border:1px solid rgba(2,44,24,.14);
    background: rgba(255,255,255,.90);
    font-weight:900;
    outline:none;
  }

  #map{
    height: 600px;
    border: 1px solid rgba(2,44,24,.12);
    border-radius: 18px;
    overflow:hidden;
    background: rgba(255,255,255,.9);
  }

  .legend{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
  .chip{
    padding:8px 10px;border-radius:999px;
    border:1px solid rgba(2,44,24,.12);
    background: rgba(255,255,255,.88);
    font-weight:900;display:flex;align-items:center;gap:8px;
  }
  .dot{width:10px;height:10px;border-radius:999px;display:inline-block}
</style>
@endpush

@section('content')

<div class="page-title">
  <div>
    <h2>🗺️ Peta Titik Jemput Saya</h2>
    <p>Lihat status setoran jemput + posisi petugas realtime (jika tracking aktif).</p>
  </div>
  <div class="row-flex" style="display:flex;gap:10px;flex-wrap:wrap">
    <a class="btn" href="{{ route('user.dashboard') }}">⬅️ Dashboard</a>
    <a class="btn" href="{{ route('user.setoran.index') }}">📦 Riwayat Setoran</a>
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
        <option value="diambil">DIAMBIL</option>
        <option value="selesai">SELESAI</option>
        <option value="ditolak">DITOLAK</option>
      </select>
    </div>
  </div>

  <div id="map"></div>

  <div class="legend">
    <div class="chip"><span class="dot" style="background:#22c55e"></span>Titik Jemput Saya</div>
    <div class="chip"><span class="dot" style="background:#f59e0b"></span>Pending</div>
    <div class="chip"><span class="dot" style="background:#3b82f6"></span>Diambil</div>
    <div class="chip"><span class="dot" style="background:#9ca3af"></span>Selesai</div>
    <div class="chip"><span class="dot" style="background:#ef4444"></span>Ditolak</div>
    <div class="chip">🚛 = posisi petugas realtime</div>
    <div class="chip">🧭 = rute petugas → titik jemput</div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
  const dataUrl = "{{ route('user.map.data') }}";
  const detailUrlBase = "{{ url('/user/setoran') }}";

  const map = L.map('map').setView([0.5071, 101.4478], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

  const pickupMarkers = new Map(); // id -> marker
  const truckMarkers  = new Map(); // id -> marker
  const routeLines    = new Map(); // id -> {outline, main}
  const routeCache    = new Map(); // id -> {fromLat, fromLng, at, routeInfo}

  const truckIcon = L.divIcon({ html: "🚛", className: "", iconSize: [28,28], iconAnchor:[14,14] });

  function colorByStatus(status){
    const s = String(status||'').toLowerCase();
    if(s === 'pending') return '#f59e0b';
    if(s === 'diambil') return '#3b82f6';
    if(s === 'selesai') return '#9ca3af';
    if(s === 'ditolak') return '#ef4444';
    return '#22c55e';
  }

  function circleIcon(color){
    return L.divIcon({
      className: "",
      html: `<div style="width:16px;height:16px;border-radius:999px;background:${color};
        border:3px solid rgba(255,255,255,.95);box-shadow:0 10px 24px rgba(0,0,0,.18);"></div>`,
      iconSize:[16,16], iconAnchor:[8,8]
    });
  }

  function fmtKm(m){ return (m/1000).toFixed(1) + " km"; }
  function fmtEta(seconds){
    const m = Math.round(seconds/60);
    if(m < 60) return m + " menit";
    const h = Math.floor(m/60);
    const r = m % 60;
    return h + " jam " + r + " menit";
  }

  function shouldShow(status, filter){
    if(filter === '__all__') return true;
    return String(status||'').toLowerCase() === filter;
  }

  function popupHtml(it, routeInfo){
    const s = String(it.status||'').toUpperCase();
    const addr = it.alamat ? it.alamat : '-';
    const seen = it.petugas_last_seen ? it.petugas_last_seen : '-';
    const hasPetugas = (it.petugas_id != null && it.petugas_id !== undefined && it.petugas_id !== '');
    const hasTruck = (it.petugas_lat != null && it.petugas_lat !== '' &&
                     it.petugas_lng != null && it.petugas_lng !== '' &&
                     !isNaN(Number(it.petugas_lat)) && !isNaN(Number(it.petugas_lng)) &&
                     Number(it.petugas_lat) !== 0 && Number(it.petugas_lng) !== 0);
    const petName = it.petugas_name || 'Petugas';

    const petugasInfo = hasTruck
      ? `🚛 ${petName} - Tracking aktif`
      : hasPetugas
        ? `${petName} - Belum mulai tracking`
        : 'Belum ada petugas';

    const routePart = routeInfo
      ? `<div style="margin-top:6px"><b>ETA:</b> ${routeInfo.eta} • <b>Jarak:</b> ${routeInfo.dist}</div>`
      : `<div style="margin-top:6px"><b>ETA/Jarak:</b> -</div>`;

    return `
      <div style="font-family:Arial;min-width:240px">
        <b>Setoran #${it.id}</b><br>
        <div style="margin-top:6px"><b>Status:</b> ${s}</div>
        <div style="margin-top:6px"><b>Alamat:</b><br>${addr}</div>
        <div style="margin-top:6px"><b>Koordinat:</b> ${it.lat.toFixed(6)}, ${it.lng.toFixed(6)}</div>
        <div style="margin-top:6px"><b>Petugas:</b> ${petugasInfo}</div>
        <div style="margin-top:6px"><b>Last seen:</b> ${seen}</div>
        ${routePart}
        <div style="margin-top:10px">
          <a href="${detailUrlBase}/${it.id}" style="display:inline-block;padding:8px 10px;border:1px solid #333;border-radius:10px;text-decoration:none">Detail</a>
          <a target="_blank" href="https://www.google.com/maps?q=${it.lat},${it.lng}" style="display:inline-block;padding:8px 10px;border:1px solid #0b6b4d;border-radius:10px;text-decoration:none;margin-left:6px">Maps</a>
        </div>
      </div>
    `;
  }

  function clearRoute(id){
    if(routeLines.has(id)){
      const r = routeLines.get(id);
      if(r.outline) map.removeLayer(r.outline);
      if(r.main) map.removeLayer(r.main);
      routeLines.delete(id);
    }
  }

  function setRoute(id, coordsLatLng){
    clearRoute(id);
    const outline = L.polyline(coordsLatLng, { weight: 11, opacity: 0.28 }).addTo(map);
    const main    = L.polyline(coordsLatLng, { weight: 7, opacity: 0.9 }).addTo(map);
    routeLines.set(id, { outline, main });
  }

  async function drawRouteIfNeeded(it){
    const hasTruck = (it.petugas_lat != null && it.petugas_lat !== '' &&
                     it.petugas_lng != null && it.petugas_lng !== '' &&
                     !isNaN(it.petugas_lat) && !isNaN(it.petugas_lng) &&
                     it.petugas_lat !== 0 && it.petugas_lng !== 0);
    if(!hasTruck){
      clearRoute(it.id);
      return null;
    }

    const now = Date.now();
    const cache = routeCache.get(it.id);

    const movedEnough = !cache
      ? true
      : (Math.abs(cache.fromLat - it.petugas_lat) + Math.abs(cache.fromLng - it.petugas_lng)) > 0.0002; // ~20m-ish

    const timeEnough = !cache ? true : (now - cache.at) > 5000; // 5 detik

    if(!movedEnough && !timeEnough){
      return cache?.routeInfo ?? null;
    }

    const url = `https://router.project-osrm.org/route/v1/driving/${it.petugas_lng},${it.petugas_lat};${it.lng},${it.lat}?overview=full&geometries=geojson`;

    try{
      const res = await fetch(url, { cache: "no-store" });
      const data = await res.json();
      if(!data.routes || !data.routes[0]) return null;

      const r = data.routes[0];
      const coords = r.geometry.coordinates.map(c => [c[1], c[0]]);
      setRoute(it.id, coords);

      const routeInfo = { eta: fmtEta(r.duration), dist: fmtKm(r.distance) };
      routeCache.set(it.id, { fromLat: it.petugas_lat, fromLng: it.petugas_lng, at: now, routeInfo });
      return routeInfo;
    }catch(e){
      return null;
    }
  }

  async function refresh(){
    try{
      const filter = document.getElementById('statusFilter').value;
      const res = await fetch(dataUrl, { cache: "no-store" });
      if(!res.ok) return;

      const data = await res.json();
      const items = data.items || [];

      document.getElementById('infoText').textContent =
        `Total titik jemput: ${items.length} • Realtime update`;

      let bounds = [];
      const alivePickup = new Set();
      const aliveTruck  = new Set();
      const aliveRoute  = new Set();

      for(const it of items){
        const ok = shouldShow(it.status, filter);
        alivePickup.add(it.id);

        const icon = circleIcon(colorByStatus(it.status));
        if(!pickupMarkers.has(it.id)){
          const m = L.marker([it.lat, it.lng], { icon }).addTo(map);
          pickupMarkers.set(it.id, m);
        }else{
          pickupMarkers.get(it.id).setLatLng([it.lat, it.lng]);
          pickupMarkers.get(it.id).setIcon(icon);
        }

        const routeInfo = await drawRouteIfNeeded(it);
        pickupMarkers.get(it.id).bindPopup(popupHtml(it, routeInfo));
        pickupMarkers.get(it.id).setOpacity(ok ? 1 : 0);
        if(ok) bounds.push([it.lat, it.lng]);

        const hasPetugas = (it.petugas_id != null && it.petugas_id !== undefined && it.petugas_id !== '');
        const hasTruck = (hasPetugas &&
          it.petugas_lat != null && it.petugas_lat !== '' &&
          it.petugas_lng != null && it.petugas_lng !== '' &&
          !isNaN(Number(it.petugas_lat)) && !isNaN(Number(it.petugas_lng)) &&
          Number(it.petugas_lat) !== 0 && Number(it.petugas_lng) !== 0);

        if(hasTruck){
          const petLat = Number(it.petugas_lat);
          const petLng = Number(it.petugas_lng);

          aliveTruck.add(it.id);
          if(!truckMarkers.has(it.id)){
            const tm = L.marker([petLat, petLng], { icon: truckIcon }).addTo(map);
            tm.bindPopup(`<b>🚛 Petugas</b><br>Setoran #${it.id}<br>Last seen: ${it.petugas_last_seen || '-'}`);
            truckMarkers.set(it.id, tm);
          }else{
            truckMarkers.get(it.id).setLatLng([petLat, petLng]);
            truckMarkers.get(it.id).setPopupContent(`<b>🚛 Petugas</b><br>Setoran #${it.id}<br>Last seen: ${it.petugas_last_seen || '-'}`);
          }

          truckMarkers.get(it.id).setOpacity(ok ? 1 : 0);
          if(ok) bounds.push([petLat, petLng]);

          if(ok) aliveRoute.add(it.id);
          else clearRoute(it.id);
        }else{
          if(truckMarkers.has(it.id)){
            map.removeLayer(truckMarkers.get(it.id));
            truckMarkers.delete(it.id);
          }
          clearRoute(it.id);
        }
      }

      for(const [id, m] of pickupMarkers){
        if(!alivePickup.has(id)){
          map.removeLayer(m);
          pickupMarkers.delete(id);
          clearRoute(id);
          if(truckMarkers.has(id)){
            map.removeLayer(truckMarkers.get(id));
            truckMarkers.delete(id);
          }
        }
      }
      for(const [id, m] of truckMarkers){
        if(!aliveTruck.has(id)){
          map.removeLayer(m);
          truckMarkers.delete(id);
        }
      }
      for(const [id] of routeLines){
        if(!aliveRoute.has(id)){
          clearRoute(id);
        }
      }

      if(bounds.length){
        map.fitBounds(L.latLngBounds(bounds).pad(0.2));
      }
    }catch(e){}
  }

  document.getElementById('statusFilter').addEventListener('change', refresh);

  refresh();
  setInterval(refresh, 1000);
</script>
@endpush
