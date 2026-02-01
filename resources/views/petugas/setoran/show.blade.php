@extends('layouts.petugas')

@section('title', 'Detail Tugas')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
    /* Hero Header */
    .page-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        padding: 24px 0 60px; color: #fff; border-radius: 0 0 30px 30px;
        margin-bottom: -40px; position: relative; z-index: 1;
        box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
    }

    .header-content {
        max-width: 1100px; margin: 0 auto; padding: 0 16px;
        display: flex; justify-content: space-between; align-items: flex-start;
    }

    .btn-back {
        background: rgba(255,255,255,0.2); color: #fff; padding: 8px 14px; border-radius: 10px;
        text-decoration: none; font-weight: 700; font-size: 0.85rem; backdrop-filter: blur(4px);
    }
    .btn-back:hover { background: rgba(255,255,255,0.3); }

    /* Layout Grid */
    .main-grid {
        display: grid; grid-template-columns: 3fr 2fr; gap: 20px;
        max-width: 1100px; margin: 0 auto; padding: 0 16px; position: relative; z-index: 10;
    }
    @media (max-width: 860px) { .main-grid { grid-template-columns: 1fr; } }

    /* Cards */
    .card {
        background: #fff; border-radius: 16px; border: 1px solid #e5e7eb;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 20px;
    }
    .card-header {
        padding: 16px 20px; border-bottom: 1px solid #e5e7eb; background: #f9fafb;
        font-weight: 700; color: #1f2937; display: flex; justify-content: space-between; align-items: center;
    }
    .card-body { padding: 20px; }

    /* Customer Info */
    .user-row { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .avatar {
        width: 48px; height: 48px; background: #ecfdf5; color: #059669; border-radius: 50%;
        display: grid; place-items: center; font-weight: 700; font-size: 1.2rem;
    }
    .user-details h4 { margin: 0; font-size: 1rem; color: #111827; }
    .user-details p { margin: 2px 0 0; color: #6b7280; font-size: 0.85rem; }

    .address-box {
        background: #fffbeb; border: 1px solid #fcd34d; padding: 12px; border-radius: 10px;
        color: #92400e; font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px;
        display: flex; gap: 10px; align-items: flex-start;
    }

    /* Item List */
    .item-row {
        display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #e5e7eb;
    }
    .item-row:last-child { border-bottom: none; }
    .item-name { font-weight: 600; color: #374151; }
    .item-sub { font-size: 0.8rem; color: #6b7280; }
    .item-price { font-weight: 700; color: #10b981; font-family: monospace; }

    /* Map & Tracking */
    #map { height: 350px; width: 100%; z-index: 1; }

    .tracking-stats {
        display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1px; background: #e5e7eb; border-top: 1px solid #e5e7eb;
    }
    .ts-box { background: #fff; padding: 10px; text-align: center; }
    .ts-label { font-size: 0.7rem; color: #6b7280; text-transform: uppercase; font-weight: 700; }
    .ts-value { font-size: 0.95rem; font-weight: 800; color: #111827; }

    /* Action Buttons */
    .action-panel { display: flex; flex-direction: column; gap: 10px; }

    .btn {
        width: 100%; padding: 12px; border-radius: 10px; border: none; font-weight: 700;
        cursor: pointer; transition: .2s; display: flex; align-items: center; justify-content: center; gap: 8px;
        font-size: 0.9rem; text-decoration: none;
    }
    .btn-primary { background: #10b981; color: #fff; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); }
    .btn-primary:hover { background: #059669; transform: translateY(-1px); }

    .btn-danger { background: #fee2e2; color: #dc2626; }
    .btn-danger:hover { background: #fecaca; }

    .btn-track { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .btn-track:hover { background: #2563eb; color: #fff; }
    .btn-track.active { background: #2563eb; color: #fff; animation: pulse-blue 2s infinite; }

    .status-badge {
        padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;
    }
    .st-menunggu { background: #fff7ed; color: #c2410c; }
    .st-diproses { background: #eff6ff; color: #1d4ed8; }
    .st-selesai { background: #ecfdf5; color: #047857; }

    @keyframes pulse-blue {
        0% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(37, 99, 235, 0); }
        100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
    }
</style>
@endpush

@section('content')
{{-- HEADER --}}
<div class="page-header">
    <div class="header-content">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0;">Detail Jemput</h1>
            <p style="margin: 4px 0 0; opacity: 0.9;">ID Transaksi: #{{ $setoran->id }}</p>
        </div>
        <a href="{{ route('petugas.setoran.index') }}" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="main-grid">
    {{-- LEFT COL: INFO --}}
    <div>
        {{-- Card Customer --}}
        <div class="card">
            <div class="card-header">
                <span>Informasi Pelanggan</span>
                @php
                    $stClass = match($setoran->status) {
                        'menunggu' => 'st-menunggu',
                        'diproses', 'dijemput' => 'st-diproses',
                        'selesai' => 'st-selesai',
                        default => 'st-menunggu'
                    };
                @endphp
                <span class="status-badge {{ $stClass }}">{{ ucfirst($setoran->status) }}</span>
            </div>
            <div class="card-body">
                <div class="user-row">
                    <div class="avatar">{{ substr($setoran->user->name ?? 'U', 0, 1) }}</div>
                    <div class="user-details">
                        <h4>{{ $setoran->user->name ?? 'User' }}</h4>
                        <p>{{ $setoran->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                </div>

                <div class="address-box">
                    <i class="fa-solid fa-location-dot" style="margin-top:2px;"></i>
                    <div>
                        <div style="font-weight:700; margin-bottom:2px;">Alamat Jemput:</div>
                        {{ $setoran->alamat ?? 'Tidak ada alamat' }}
                    </div>
                </div>

                <div style="font-size:0.85rem; font-weight:700; color:#374151; margin-bottom:10px;">Daftar Barang:</div>
                @foreach($setoran->items as $d)
                    <div class="item-row">
                        <div>
                            <div class="item-name">{{ $d->kategori->nama_sampah ?? 'Item' }}</div>
                            <div class="item-sub">{{ $d->kategori->masterKategori->nama_kategori ?? '-' }} • {{ $d->jumlah }} {{ $d->satuan }}</div>
                        </div>
                        <div class="item-price">
                            Rp {{ number_format($d->subtotal) }}
                        </div>
                    </div>
                @endforeach

                <div style="display:flex; justify-content:space-between; margin-top:16px; padding-top:16px; border-top:1px dashed #e5e7eb;">
                    <div style="font-weight:700; color:#374151;">Total Estimasi</div>
                    <div style="font-weight:800; font-size:1.1rem; color:#10b981;">Rp {{ number_format($setoran->estimasi_total) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT COL: ACTION & MAP --}}
    <div>
        {{-- Card Map --}}
        @if($setoran->metode === 'jemput' && $setoran->latitude && $setoran->longitude)
        <div class="card" style="border-color:#10b981; border-width:2px;">
            <div class="card-header" style="background:#ecfdf5; color:#065f46; border-bottom-color:#a7f3d0;">
                <i class="fa-solid fa-map-location-dot"></i> Tracking Lokasi
            </div>

            <div style="position:relative;">
                <div id="map"></div>
            </div>

            <div class="tracking-stats">
                <div class="ts-box">
                    <div class="ts-label">ETA</div>
                    <div class="ts-value" id="etaText">-</div>
                </div>
                <div class="ts-box">
                    <div class="ts-label">Jarak</div>
                    <div class="ts-value" id="distText">-</div>
                </div>
                <div class="ts-box">
                    <div class="ts-label">GPS</div>
                    <div class="ts-value" id="gpsText" style="font-size:0.7rem; line-height:1.2;">Off</div>
                </div>
            </div>

            <div class="card-body" style="padding:12px; background:#f9fafb;">
                <div id="routeInfo" style="font-size:0.8rem; color:#6b7280; text-align:center;">
                    <i class="fa-solid fa-info-circle"></i> Rute akan muncul saat tracking aktif.
                </div>
            </div>
        </div>
        @endif

        {{-- Card Actions --}}
        <div class="card">
            <div class="card-header">Tindakan</div>
            <div class="card-body action-panel">

                @if($setoran->status === 'pending')
                    <form method="POST" action="{{ route('petugas.setoran.ambil', $setoran->id) }}">
                        @csrf
                        <button class="btn btn-primary" type="submit">
                            <i class="fa-solid fa-hand-holding-hand"></i> Ambil Order Ini
                        </button>
                    </form>
                @endif

                @if($setoran->petugas_id && $setoran->petugas_id == auth()->id())

                    @if($setoran->metode === 'jemput')
                        <button class="btn btn-track" type="button" id="startTrackBtn">
                            <i class="fa-solid fa-satellite-dish"></i> <span>Mulai Tracking (Live)</span>
                        </button>

                        <a target="_blank" href="https://www.google.com/maps?q={{ $setoran->latitude }},{{ $setoran->longitude }}" class="btn" style="background:#fff; border:1px solid #e5e7eb; color:#374151;">
                            <i class="fa-solid fa-map-marked-alt"></i> Buka Google Maps
                        </a>
                    @endif

                    <div style="height:1px; background:#e5e7eb; margin:5px 0;"></div>

                    <form method="POST" action="{{ route('petugas.setoran.status', $setoran->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="selesai">
                        <button class="btn btn-primary" type="submit" onclick="return confirm('Selesaikan order ini?')">
                            <i class="fa-solid fa-check-circle"></i> Selesai / Barang Diterima
                        </button>
                    </form>

                    <form method="POST" action="{{ route('petugas.setoran.status', $setoran->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="ditolak">
                        <button class="btn btn-danger" type="submit" onclick="return confirm('Yakin tolak order ini?')">
                            <i class="fa-solid fa-xmark"></i> Tolak / Batalkan
                        </button>
                    </form>

                @endif

                @if($setoran->status === 'selesai')
                    <div style="text-align:center; color:#059669; font-weight:700;">
                        <i class="fa-solid fa-circle-check" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                        Order Selesai
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    // Config Data
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const postUrl = "{{ route('petugas.setoran.lokasi', $setoran->id) }}";

    // Check if map needed
    const hasMap = @json($setoran->metode === 'jemput' && $setoran->latitude && $setoran->longitude);
    const tujuanLat = Number("{{ $setoran->latitude }}");
    const tujuanLng = Number("{{ $setoran->longitude }}");

    // --- LEAFLET & LOGIC ---
    if(hasMap) {
        const truckIcon = L.divIcon({
            html: `<div style="background:#fff; border-radius:50%; width:32px; height:32px; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 8px rgba(0,0,0,0.2); font-size:1.2rem;">🚛</div>`,
            className: "", iconSize: [32,32], iconAnchor:[16,16]
        });

        const map = L.map('map', { zoomControl: false }).setView([tujuanLat, tujuanLng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        const tujuanMarker = L.marker([tujuanLat, tujuanLng]).addTo(map)
            .bindPopup("<b>Lokasi User</b><br>{{ $setoran->alamat }}").openPopup();

        let petugasMarker = null;
        let routeOutline = null;
        let routeMain = null;
        let routeCoords = null;

        // Utils
        function fmtKm(m){ return (m/1000).toFixed(1) + " km"; }
        function fmtEta(seconds){
            const m = Math.round(seconds/60);
            if(m < 60) return m + " mnt";
            const h = Math.floor(m/60);
            const r = m % 60;
            return h + "j " + r + "m";
        }
        function haversineMeters(aLat,aLng,bLat,bLng){
            const R = 6371000;
            const toRad = d => d * Math.PI/180;
            const dLat = toRad(bLat-aLat), dLng = toRad(bLng-aLng);
            const s1 = Math.sin(dLat/2), s2 = Math.sin(dLng/2);
            const c = 2*Math.asin(Math.sqrt(s1*s1 + Math.cos(toRad(aLat))*Math.cos(toRad(bLat))*s2*s2));
            return R*c;
        }

        // Animation Vars
        let animFrom = null;
        let animTo = null;
        let animStart = 0;
        let animDur = 700;

        function ensureMarker(lat, lng){
            if(!petugasMarker){
                petugasMarker = L.marker([lat,lng], { icon: truckIcon }).addTo(map).bindPopup("Posisi Anda");
                // Auto fit bounds biar kelihatan dua-duanya
                const group = new L.featureGroup([tujuanMarker, petugasMarker]);
                map.fitBounds(group.getBounds().pad(0.2));
            }
        }

        function setTargetPosition(lat, lng){
            ensureMarker(lat, lng);
            const current = petugasMarker.getLatLng();
            animFrom = {lat: current.lat, lng: current.lng};
            animTo   = {lat: lat, lng: lng}; // Simple direct anim for local
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

        // OSRM Route
        function setRoute(coordsLatLng){
            routeCoords = coordsLatLng;
            if(routeOutline) routeOutline.setLatLngs(coordsLatLng);
            else routeOutline = L.polyline(coordsLatLng, { weight: 8, opacity: 0.4, color: '#10b981' }).addTo(map);

            if(routeMain) routeMain.setLatLngs(coordsLatLng);
            else routeMain = L.polyline(coordsLatLng, { weight: 5, opacity: 1, color: '#059669' }).addTo(map);
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
                document.getElementById('routeInfo').innerHTML = `<i class="fa-solid fa-route"></i> Rute aktif. Mengarah ke titik jemput.`;
            } catch(e){}
        }

        // --- TRACKING LOGIC ---
        let watchId = null;
        let lastSentAt = 0;
        let lastSentLat = null, lastSentLng = null;
        const MIN_INTERVAL_MS = 2000; // Kirim ke server tiap 2 detik max
        const MIN_DISTANCE_M  = 5;    // Atau jika pindah 5 meter

        async function sendLocation(lat, lng){
            // Kirim background tanpa blokir UI
            fetch(postUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ lat, lng })
            }).catch(e => console.log('Loc send err:', e));
        }

        const trackBtn = document.getElementById('startTrackBtn');
        const gpsEl = document.getElementById('gpsText');

        if(trackBtn) {
            trackBtn.addEventListener('click', () => {
                if(!navigator.geolocation){ alert("Browser tidak support GPS."); return; }

                if(watchId){ // Stop
                    navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                    trackBtn.innerHTML = '<i class="fa-solid fa-satellite-dish"></i> <span>Mulai Tracking (Live)</span>';
                    trackBtn.classList.remove('active');
                    gpsEl.innerText = "Paused";
                    return;
                }

                // Start
                trackBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Tracking Aktif...</span>';
                trackBtn.classList.add('active');

                watchId = navigator.geolocation.watchPosition(async (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;

                    gpsEl.innerHTML = `<span style="color:#10b981">ON</span> • ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                    setTargetPosition(lat, lng);

                    const now = Date.now();
                    let dist = 9999;
                    if(lastSentLat !== null) dist = haversineMeters(lastSentLat, lastSentLng, lat, lng);

                    // Kirim ke server jika pindah cukup jauh ATAU waktu sudah berlalu
                    if(dist >= MIN_DISTANCE_M || (now - lastSentAt) >= MIN_INTERVAL_MS){
                        lastSentAt = now;
                        lastSentLat = lat; lastSentLng = lng;
                        sendLocation(lat, lng); // Update DB
                        drawRoute(lat, lng);    // Update visual route OSRM
                    }
                }, (err) => {
                    alert("Gagal ambil lokasi: " + err.message);
                    trackBtn.click(); // Auto stop if error
                }, {
                    enableHighAccuracy: true,
                    maximumAge: 0,
                    timeout: 10000
                });
            });
        }

        // Init location from DB if exists
        @if($setoran->petugas_latitude && $setoran->petugas_longitude)
            const initLat = Number("{{ $setoran->petugas_latitude }}");
            const initLng = Number("{{ $setoran->petugas_longitude }}");
            setTargetPosition(initLat, initLng);
            drawRoute(initLat, initLng);
        @endif
    }
</script>
@endpush
