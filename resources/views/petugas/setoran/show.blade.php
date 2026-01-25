@extends('layouts.petugas') {{-- sesuaikan nama file layout petugas kamu --}}

@section('title', 'Detail Jemput')

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
  .btn-danger{border-color: rgba(239,68,68,.45); background: rgba(239,68,68,.10); color:#ffd2d2}
  .btn-ok{border-color: rgba(34,197,94,.45); background: rgba(34,197,94,.14)}

  .muted{color:var(--muted);font-size:13px}
  .flex{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
  .divider{margin:12px 0;border:0;border-top:1px solid rgba(255,255,255,.10)}

  #map{height:440px;border:1px solid rgba(255,255,255,.12);border-radius:12px}

  .pill{
    display:inline-flex;align-items:center;gap:8px;
    padding:6px 10px;border:1px solid rgba(255,255,255,.10);
    border-radius:999px;background: rgba(255,255,255,.03);
    color: var(--text);
    font-size:12px;
  }
  .kpi{display:flex;gap:10px;flex-wrap:wrap;margin-top:10px}
  .kpi .pill b{margin-right:6px;color:var(--muted)}

  .actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-top:10px}
</style>
@endpush

@section('content')
<div class="page-bg">
  <h2>Petugas - Detail Setoran Jemput</h2>

  @if(session('success'))
    <div class="box" style="border-color: rgba(34,197,94,.45); margin-bottom:12px">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="box" style="border-color: rgba(239,68,68,.45); margin-bottom:12px">
      {{ session('error') }}
    </div>
  @endif

  <div class="box">
    <div class="flex">
      <div><b>User:</b> {{ $setoran->user->name ?? '-' }}</div>
      <div><b>Status:</b> {{ strtoupper($setoran->status) }}</div>
    </div>

    <div class="muted" style="margin-top:8px;">
      <b>Alamat:</b> {{ $setoran->alamat ?? '-' }} <br>
      <b>Titik Jemput:</b> {{ $setoran->latitude }}, {{ $setoran->longitude }}
    </div>

    <div class="actions">
      @if($setoran->status === 'pending')
        <form method="POST" action="{{ route('petugas.setoran.ambil', $setoran->id) }}">
          @csrf
          <button class="btn btn-ok" type="submit">Ambil Order</button>
        </form>
      @endif

      @if($setoran->petugas_id && $setoran->petugas_id == auth()->id())
        <form method="POST" action="{{ route('petugas.setoran.status', $setoran->id) }}">
          @csrf
          <input type="hidden" name="status" value="selesai">
          <button class="btn btn-ok" type="submit">Tandai Selesai</button>
        </form>

        <form method="POST" action="{{ route('petugas.setoran.status', $setoran->id) }}">
          @csrf
          <input type="hidden" name="status" value="ditolak">
          <button class="btn btn-danger" type="submit">Tolak</button>
        </form>

        <button class="btn" type="button" id="startTrackBtn">Mulai Tracking (Realtime)</button>
        <span class="muted" id="trackStatus">Tracking belum aktif.</span>
      @endif
    </div>

    <div class="kpi">
      <span class="pill"><b>ETA:</b> <span id="etaText">-</span></span>
      <span class="pill"><b>Jarak:</b> <span id="distText">-</span></span>
      <span class="pill"><b>GPS:</b> <span id="gpsText">-</span></span>
    </div>

    <div style="margin-top:12px" id="map"></div>
    <div class="muted" id="routeInfo" style="margin-top:8px;">Rute belum dihitung.</div>
  </div>

  <div style="margin-top:12px;">
    <a class="btn" href="{{ route('petugas.setoran.index') }}">⬅️ Kembali</a>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const postUrl = "{{ route('petugas.setoran.lokasi', $setoran->id) }}";

  const tujuanLat = Number("{{ $setoran->latitude }}");
  const tujuanLng = Number("{{ $setoran->longitude }}");

  const truckIcon = L.divIcon({ html: "🚛", className: "", iconSize: [28,28], iconAnchor:[14,14] });

  const map = L.map('map').setView([tujuanLat, tujuanLng], 15);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

  const tujuanMarker = L.marker([tujuanLat, tujuanLng]).addTo(map).bindPopup("Titik Jemput");

  let petugasMarker = null;
  let routeOutline = null;
  let routeMain = null;
  let routeCoords = null; // array [lat,lng]

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

  // smooth movement
  let animFrom = null;
  let animTo = null;
  let animStart = 0;
  let animDur = 700;

  function ensureMarker(lat, lng){
    if(!petugasMarker){
      petugasMarker = L.marker([lat,lng], { icon: truckIcon }).addTo(map).bindPopup("Truk Petugas");
      map.setView([lat,lng], 15);
    }
  }

  function setTargetPosition(lat, lng){
    const snapped = snapToRoute(lat, lng);
    ensureMarker(snapped.lat, snapped.lng);

    const current = petugasMarker.getLatLng();
    animFrom = {lat: current.lat, lng: current.lng};
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

  // draw route OSRM
  function setRoute(coordsLatLng){
    routeCoords = coordsLatLng;
    if(routeOutline) routeOutline.setLatLngs(coordsLatLng);
    else routeOutline = L.polyline(coordsLatLng, { weight: 11, opacity: 0.35 }).addTo(map);

    if(routeMain) routeMain.setLatLngs(coordsLatLng);
    else routeMain = L.polyline(coordsLatLng, { weight: 7, opacity: 0.9 }).addTo(map);

    const fg = L.featureGroup([tujuanMarker, L.polyline(coordsLatLng)]);
    map.fitBounds(fg.getBounds().pad(0.2));
  }

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
      document.getElementById('routeInfo').innerText = "Rute & ETA akan update otomatis saat truk bergerak.";
    } catch(e){}
  }

  // sending location
  let watchId = null;
  let lastSentAt = 0;
  let lastSentLat = null, lastSentLng = null;

  const MIN_INTERVAL_MS = 500;
  const MIN_DISTANCE_M  = 2;

  async function sendLocation(lat, lng){
    await fetch(postUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify({ lat, lng })
    }).catch(()=>{});
  }

  document.getElementById('startTrackBtn')?.addEventListener('click', () => {
    const statusEl = document.getElementById('trackStatus');
    const gpsEl = document.getElementById('gpsText');

    if(!navigator.geolocation){
      statusEl.textContent = "Browser tidak mendukung GPS.";
      return;
    }
    if(watchId){
      statusEl.textContent = "Tracking sudah aktif.";
      return;
    }

    statusEl.textContent = "Tracking aktif (realtime)...";

    watchId = navigator.geolocation.watchPosition(async (pos) => {
      const lat = pos.coords.latitude;
      const lng = pos.coords.longitude;

      gpsEl.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

      setTargetPosition(lat, lng);

      const now = Date.now();
      const timeOk = (now - lastSentAt) >= MIN_INTERVAL_MS;

      let dist = 9999;
      if(lastSentLat !== null){
        dist = haversineMeters(lastSentLat, lastSentLng, lat, lng);
      }

      const shouldSend = timeOk || dist >= MIN_DISTANCE_M;
      if(shouldSend){
        lastSentAt = now;
        lastSentLat = lat;
        lastSentLng = lng;
        await sendLocation(lat, lng);
      }

      // route update jangan tiap 0.5 detik
      if(dist >= 20 || (now % 3000) < 600){
        drawRoute(lat, lng);
      }
    }, (err) => {
      statusEl.textContent = "Gagal ambil lokasi: " + err.message;
    }, {
      enableHighAccuracy: true,
      maximumAge: 0,
      timeout: 10000
    });
  });

  // lokasi awal dari DB (jika ada)
  @if($setoran->petugas_latitude && $setoran->petugas_longitude)
    const initLat = Number("{{ $setoran->petugas_latitude }}");
    const initLng = Number("{{ $setoran->petugas_longitude }}");
    setTargetPosition(initLat, initLng);
    drawRoute(initLat, initLng);
  @endif
</script>
@endpush
