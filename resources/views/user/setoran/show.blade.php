@extends('layouts.user')

@section('title', 'Detail Setoran')

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

  .row-flex{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
  .muted{color:var(--muted);font-size:13px;font-weight:800}

  .pill{
    display:inline-flex;align-items:center;gap:8px;
    padding:7px 10px;border-radius:999px;
    border:1px solid rgba(34,197,94,.18);
    background: rgba(34,197,94,.10);
    font-weight:1000;font-size:12px;color:var(--g1);
  }
  .pill.gray{border-color: rgba(2,44,24,.12); background: rgba(255,255,255,.72); color: var(--text);}

  .divider{border:none;border-top:1px solid rgba(2,44,24,.10);margin:14px 0}

  .items{
    margin-top:10px;
    display:grid;
    gap:10px;
  }
  .item{
    border:1px solid rgba(2,44,24,.10);
    background: rgba(255,255,255,.82);
    border-radius: 16px;
    padding: 10px 12px;
    display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;
  }
  .item b{font-weight:1000}
  .item .right{font-weight:1000;color:var(--g1)}
  .item .sub{margin-top:3px}

  .kpi{
    display:grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin-top: 12px;
  }
  @media (max-width: 860px){ .kpi{grid-template-columns:1fr} }

  .kpi-card{
    border:1px solid rgba(34,197,94,.18);
    background: rgba(34,197,94,.10);
    border-radius: 18px;
    padding: 10px 12px;
    display:flex;justify-content:space-between;gap:10px;align-items:center;
    font-weight:1000;color:var(--g1);
  }
  .kpi-card span{color:var(--text);font-weight:1000}

  #map{
    height: 460px;
    border-radius: 18px;
    border: 1px solid rgba(2,44,24,.12);
    overflow:hidden;
    background: rgba(255,255,255,.9);
    margin-top: 12px;
  }

  .actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
</style>
@endpush

@section('content')

<div class="page-title">
  <div>
    <h2>📄 Detail Setoran</h2>
    <p>Detail item, status, dan tracking jemput (jika metode jemput).</p>
  </div>
  <a class="btn" href="{{ route('user.setoran.index') }}">← Kembali</a>
</div>

<div class="box">
  <div class="row-flex">
    <div class="pill gray"><b>Status:</b> <span id="statusText">{{ strtoupper($setoran->status) }}</span></div>
    <div class="pill gray"><b>Metode:</b> <span>{{ strtoupper($setoran->metode) }}</span></div>
    <div class="pill"><b>Total:</b> <span>Rp {{ number_format($setoran->estimasi_total) }}</span></div>
  </div>

  <hr class="divider">

  <div class="row-flex" style="justify-content:space-between;align-items:flex-end">
    <div>
      <b style="font-weight:1000">🧾 Item</b>
      <div class="muted" style="margin-top:4px">Daftar item yang kamu setor.</div>
    </div>
  </div>

  <div class="items">
    @foreach($setoran->items as $d)
      <div class="item">
        <div>
          <b>{{ $d->kategori->nama_sampah ?? '-' }}</b>
          <div class="muted sub">
            ({{ $d->kategori->masterKategori->nama_kategori ?? '-' }})
            • {{ $d->jumlah }} {{ $d->satuan ?? '' }}
          </div>
        </div>
        <div class="right">
          Rp {{ number_format($d->subtotal) }}
        </div>
      </div>
    @endforeach
  </div>

  @if($setoran->metode === 'jemput' && $setoran->latitude && $setoran->longitude)
    <hr class="divider">

    <div class="muted">
      <b>Alamat:</b> {{ $setoran->alamat ?? '-' }} <br>
      <b>Titik Jemput:</b> {{ $setoran->latitude }}, {{ $setoran->longitude }}
    </div>

    <div class="kpi">
      <div class="kpi-card"><div>ETA</div><span id="etaText">-</span></div>
      <div class="kpi-card"><div>Jarak</div><span id="distText">-</span></div>
      <div class="kpi-card"><div>Last seen</div><span id="seenText">-</span></div>
    </div>

    <div id="map"></div>

    <div class="muted" id="petugasInfo" style="margin-top:10px;">
      Menunggu petugas mengambil order...
    </div>

    <div class="actions">
      <a class="btn btn-primary" target="_blank" rel="noopener"
         href="https://www.google.com/maps?q={{ $setoran->latitude }},{{ $setoran->longitude }}">
        🗺️ Buka Google Maps (Titik Jemput)
      </a>
    </div>
  @endif
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
  const tujuanLat = Number("{{ $setoran->latitude }}");
  const tujuanLng = Number("{{ $setoran->longitude }}");
  const urlLoc = "{{ route('user.setoran.petugas_location', $setoran->id) }}";

  const truckIcon = L.divIcon({ html: "🚛", className: "", iconSize: [28,28], iconAnchor:[14,14] });

  @if($setoran->metode === 'jemput' && $setoran->latitude && $setoran->longitude)
  const map = L.map('map').setView([tujuanLat, tujuanLng], 15);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

  const tujuanMarker = L.marker([tujuanLat, tujuanLng]).addTo(map).bindPopup("Titik Jemput");

  let petugasMarker = null;
  let routeOutline = null;
  let routeMain = null;
  let routeCoords = null;

  let lastKnownLat = null, lastKnownLng = null;

  function fmtKm(m){ return (m/1000).toFixed(1) + " km"; }
  function fmtEta(seconds){
    const m = Math.round(seconds/60);
    if(m < 60) return m + " menit";
    const h = Math.floor(m/60);
    const r = m % 60;
    return h + " jam " + r + " menit";
  }

  function haversineMeters(aLat,aLng,bLat,bLng){
    const R = 6371000;
    const toRad = d => d * Math.PI/180;
    const dLat = toRad(bLat-aLat), dLng = toRad(bLng-aLng);
    const s1 = Math.sin(dLat/2), s2 = Math.sin(dLng/2);
    const c = 2*Math.asin(Math.sqrt(s1*s1 + Math.cos(toRad(aLat))*Math.cos(toRad(bLat))*s2*s2));
    return R*c;
  }

  function closestPointOnSegment(p, a, b){
    const ax=a.lng, ay=a.lat, bx=b.lng, by=b.lat, px=p.lng, py=p.lat;
    const abx = bx-ax, aby = by-ay;
    const apx = px-ax, apy = py-ay;
    const ab2 = abx*abx + aby*aby;
    const t = ab2 ? (apx*abx + apy*aby)/ab2 : 0;
    const tt = Math.max(0, Math.min(1, t));
    return { lat: ay + aby*tt, lng: ax + abx*tt };
  }

  function snapToRoute(lat, lng){
    if(!routeCoords || routeCoords.length < 2) return {lat, lng};
    const p = {lat, lng};
    let best = null, bestD = Infinity;
    for(let i=0;i<routeCoords.length-1;i++){
      const a = {lat: routeCoords[i][0], lng: routeCoords[i][1]};
      const b = {lat: routeCoords[i+1][0], lng: routeCoords[i+1][1]};
      const c = closestPointOnSegment(p, a, b);
      const d = haversineMeters(p.lat, p.lng, c.lat, c.lng);
      if(d < bestD){ bestD = d; best = c; }
    }
    return best || {lat, lng};
  }

  let animFrom=null, animTo=null, animStart=0, animDur=700;

  function ensureMarker(lat, lng){
    if(!petugasMarker){
      petugasMarker = L.marker([lat,lng], { icon: truckIcon }).addTo(map).bindPopup("🚛 Truk Petugas");
    }
  }

  function setTargetPosition(lat, lng){
    ensureMarker(lat, lng);

    let targetLat = lat, targetLng = lng;
    if(routeCoords && routeCoords.length > 0){
      const snapped = snapToRoute(lat, lng);
      targetLat = snapped.lat;
      targetLng = snapped.lng;
    }

    const cur = petugasMarker.getLatLng();
    animFrom = {lat: cur.lat, lng: cur.lng};
    animTo   = {lat: targetLat, lng: targetLng};
    animStart = performance.now();
  }

  function tick(){
    if(petugasMarker && animFrom && animTo){
      const now = performance.now();
      const t = Math.min(1, (now - animStart) / animDur);
      const lat = animFrom.lat + (animTo.lat - animFrom.lat) * t;
      const lng = animFrom.lng + (animTo.lng - animFrom.lng) * t;
      petugasMarker.setLatLng([lat, lng]);
    }
    requestAnimationFrame(tick);
  }
  requestAnimationFrame(tick);

  function setRoute(coordsLatLng){
    routeCoords = coordsLatLng;

    if(routeOutline) routeOutline.setLatLngs(coordsLatLng);
    else routeOutline = L.polyline(coordsLatLng, { weight: 11, opacity: 0.35 }).addTo(map);

    if(routeMain) routeMain.setLatLngs(coordsLatLng);
    else routeMain = L.polyline(coordsLatLng, { weight: 7, opacity: 0.9 }).addTo(map);
  }

  let lastRouteAt = 0;
  let lastRouteFromLat = null, lastRouteFromLng = null;

  async function drawRoute(fromLat, fromLng){
    const url = `https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${tujuanLng},${tujuanLat}?overview=full&geometries=geojson`;
    try{
      const res = await fetch(url, { cache: "no-store" });
      const data = await res.json();
      if(!data.routes || !data.routes[0]) return;

      const r = data.routes[0];
      const coords = r.geometry.coordinates.map(c => [c[1], c[0]]);
      setRoute(coords);

      document.getElementById('etaText').innerText = fmtEta(r.duration);
      document.getElementById('distText').innerText = fmtKm(r.distance);
    } catch(e){}
  }

  async function refreshPetugas(){
    try{
      const res = await fetch(urlLoc, { cache: "no-store" });
      if(!res.ok) return;

      const data = await res.json();

      if(data.status){
        document.getElementById('statusText').innerText = String(data.status).toUpperCase();
      }

      const info = document.getElementById('petugasInfo');
      const seenText = document.getElementById('seenText');

      if(!data.petugas_id){
        info.innerText = 'Menunggu petugas mengambil order...';
        seenText.innerText = '-';

        if(petugasMarker){ map.removeLayer(petugasMarker); petugasMarker = null; }
        if(routeOutline) map.removeLayer(routeOutline);
        if(routeMain) map.removeLayer(routeMain);
        routeOutline = null; routeMain = null; routeCoords = null;

        document.getElementById('etaText').innerText = '-';
        document.getElementById('distText').innerText = '-';
        return;
      }

      const name = data.petugas_name || 'Petugas';
      seenText.innerText = data.petugas_last_seen ?? '-';

      const hasLatLng = (data.petugas_latitude != null && data.petugas_latitude !== '' &&
                         data.petugas_longitude != null && data.petugas_longitude !== '');

      if(hasLatLng){
        const lat = Number(data.petugas_latitude);
        const lng = Number(data.petugas_longitude);

        if(isNaN(lat) || isNaN(lng) || lat === 0 || lng === 0){
          info.innerText = `${name} sudah mengambil order, menunggu lokasi valid...`;
          document.getElementById('etaText').innerText = '-';
          document.getElementById('distText').innerText = '-';
          if(petugasMarker){ map.removeLayer(petugasMarker); petugasMarker = null; }
          return;
        }

        lastKnownLat = lat; lastKnownLng = lng;

        ensureMarker(lat, lng);
        setTargetPosition(lat, lng);
        info.innerText = `Truk ${name} bergerak menuju titik jemput...`;

        const now = Date.now();
        const moved = (lastRouteFromLat !== null) ? haversineMeters(lastRouteFromLat,lastRouteFromLng,lat,lng) : 9999;

        if(!routeCoords || (now - lastRouteAt) >= 4000 || moved >= 15){
          lastRouteAt = now;
          lastRouteFromLat = lat;
          lastRouteFromLng = lng;
          await drawRoute(lat, lng);
          if(petugasMarker) setTargetPosition(lat, lng);
        }
      } else if(lastKnownLat !== null && lastKnownLng !== null){
        info.innerText = `Truk ${name} sinyal lemah, menampilkan lokasi terakhir...`;
        setTargetPosition(lastKnownLat, lastKnownLng);
      } else {
        info.innerText = `${name} sudah mengambil order. Petugas belum mulai tracking lokasi...`;

        if(petugasMarker){ map.removeLayer(petugasMarker); petugasMarker = null; }
        if(routeOutline) map.removeLayer(routeOutline);
        if(routeMain) map.removeLayer(routeMain);
        routeOutline = null; routeMain = null; routeCoords = null;

        document.getElementById('etaText').innerText = '-';
        document.getElementById('distText').innerText = '-';
      }
    }catch(e){}
  }

  refreshPetugas();
  setInterval(refreshPetugas, 1000);
  @endif
</script>
@endpush
