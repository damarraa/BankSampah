@extends('layouts.admin') {{-- sesuaikan nama master layout kamu --}}

@section('title', 'Detail Setoran')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>

<style>
    :root{
        --bg:#0b1220; --text:#eaf0ff; --muted:#93a4c7;
        --line:rgba(255,255,255,.10);
        --shadow:0 18px 60px rgba(0,0,0,.35);
        --radius:16px;
        --brand:#22c55e; --danger:#ef4444;
    }

    .page-bg{
        padding: 18px;
        border-radius: 18px;
        background:
            radial-gradient(1200px 600px at 20% 0%, rgba(34,197,94,.22), transparent 55%),
            radial-gradient(900px 500px at 90% 15%, rgba(59,130,246,.16), transparent 60%),
            var(--bg);
        color: var(--text);
        border: 1px solid rgba(255,255,255,.06);
        max-width: 1100px;
        margin: 0 auto;
    }

    h2{margin:0 0 14px;font-size:22px}

    .box{
        border:1px solid var(--line);
        padding:14px;
        border-radius: var(--radius);
        background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
        box-shadow: var(--shadow);
    }

    .btn{
        padding:10px 14px;border-radius:12px;border:1px solid var(--line);
        background: rgba(255,255,255,.04);
        color:var(--text);text-decoration:none;cursor:pointer;
        display:inline-flex;align-items:center;gap:8px;font-size:13px;
    }
    .btn:hover{filter: brightness(1.06)}

    .muted{color:var(--muted);font-size:13px}

    .flex{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
    .divider{margin:12px 0;border:0;border-top:1px solid rgba(255,255,255,.10)}

    #map{height:440px;border:1px solid rgba(255,255,255,.12);border-radius:12px}

    .pill{
        display:inline-flex;align-items:center;gap:8px;
        padding:6px 10px;border:1px solid rgba(255,255,255,.10);
        border-radius:999px;background: rgba(255,255,255,.03);
        color: var(--text);
    }
    .kpi{display:flex;gap:10px;flex-wrap:wrap;margin-top:10px}
    .kpi .pill b{margin-right:6px;color:var(--muted)}

    .badge{
        display:inline-flex;align-items:center;gap:6px;
        padding:6px 10px;border-radius:999px;
        border:1px solid rgba(255,255,255,.10);
        background: rgba(255,255,255,.03);
        color: var(--text);
        font-size:12px;
    }

    .actions{margin-top:14px;display:flex;gap:10px;flex-wrap:wrap}
</style>
@endpush

@section('content')
<div class="page-bg">
    <h2>Admin - Detail Setoran</h2>

    <div class="box">
        <div class="flex">
            <div><b>User:</b> {{ $setoran->user->name ?? '-' }}</div>
            <div><b>Status:</b> <span class="badge" id="statusText">{{ strtoupper($setoran->status) }}</span></div>
            <div><b>Petugas:</b> <span id="petugasName">{{ $setoran->petugas->name ?? '-' }}</span></div>
            <div><b>Metode:</b> <span class="badge">{{ strtoupper($setoran->metode ?? '-') }}</span></div>
        </div>

        @if($setoran->metode === 'jemput' && $setoran->latitude && $setoran->longitude)
            <hr class="divider">

            <div class="muted">
                <b>Alamat:</b> {{ $setoran->alamat ?? '-' }} <br>
                <b>Titik Jemput:</b> {{ $setoran->latitude }}, {{ $setoran->longitude }}
            </div>

            <div class="kpi">
                <span class="pill"><b>ETA:</b> <span id="etaText">-</span></span>
                <span class="pill"><b>Jarak:</b> <span id="distText">-</span></span>
                <span class="pill"><b>Last seen:</b> <span id="seenText">-</span></span>
            </div>

            <div style="margin-top:12px" id="map"></div>
            <div class="muted" id="petugasInfo" style="margin-top:8px;">Memuat lokasi petugas...</div>
        @else
            <p class="muted" style="margin-top:12px">Metode antar / tidak ada koordinat.</p>
        @endif
    </div>

    <div class="actions">
        <a class="btn" href="{{ route('admin.setoran.index') }}">⬅️ Kembali</a>
        <a class="btn" href="{{ route('admin.map') }}">🗺️ Peta Semua Jemput</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
  const tujuanLat = Number("{{ $setoran->latitude }}");
  const tujuanLng = Number("{{ $setoran->longitude }}");
  const urlLoc = "{{ route('admin.setoran.petugas_location', $setoran->id) }}";

  const truckIcon = L.divIcon({ html: "🚛", className: "", iconSize: [28,28], iconAnchor:[14,14] });

  const map = L.map('map').setView([tujuanLat, tujuanLng], 15);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

  L.marker([tujuanLat, tujuanLng]).addTo(map).bindPopup("Titik Jemput");

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
      petugasMarker = L.marker([lat,lng], { icon: truckIcon }).addTo(map).bindPopup("Truk Petugas");
    }
  }

  function setTargetPosition(lat, lng){
    const snapped = snapToRoute(lat, lng);
    ensureMarker(snapped.lat, snapped.lng);
    const cur = petugasMarker.getLatLng();
    animFrom = {lat: cur.lat, lng: cur.lng};
    animTo   = {lat: snapped.lat, lng: snapped.lng};
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
      const data = await res.json();

      document.getElementById('statusText').innerText = (data.status || '').toUpperCase();
      document.getElementById('petugasName').innerText = data.petugas_name || '-';

      const info = document.getElementById('petugasInfo');
      const seenText = document.getElementById('seenText');

      if(!data.petugas_id){
        info.innerText = 'Belum ada petugas mengambil order.';
        seenText.innerText = '-';
        return;
      }

      const name = data.petugas_name || 'Petugas';
      const hasLatLng = (data.petugas_latitude && data.petugas_longitude);

      if(hasLatLng){
        const lat = Number(data.petugas_latitude);
        const lng = Number(data.petugas_longitude);
        lastKnownLat = lat; lastKnownLng = lng;

        setTargetPosition(lat, lng);
        info.innerText = `Truk ${name} bergerak menuju titik jemput...`;
        seenText.innerText = data.petugas_last_seen ?? '-';

        const now = Date.now();
        const moved = (lastRouteFromLat !== null) ? haversineMeters(lastRouteFromLat,lastRouteFromLng,lat,lng) : 9999;

        if((now - lastRouteAt) >= 4000 || moved >= 15){
          lastRouteAt = now;
          lastRouteFromLat = lat;
          lastRouteFromLng = lng;
          await drawRoute(lat, lng);
        }
      } else if(lastKnownLat !== null){
        info.innerText = `Truk ${name} sinyal lemah, menampilkan lokasi terakhir...`;
        seenText.innerText = data.petugas_last_seen ?? '-';
        setTargetPosition(lastKnownLat, lastKnownLng);
      } else {
        info.innerText = `${name} sudah mengambil order, menunggu lokasi terkirim...`;
        seenText.innerText = data.petugas_last_seen ?? '-';
      }
    } catch(e){}
  }

  refreshPetugas();
  setInterval(refreshPetugas, 500);
</script>
@endpush
