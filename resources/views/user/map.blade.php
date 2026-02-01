@extends('layouts.user')
@section('title', 'Live Tracking')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />

    <style>
        :root {
            --brand: #10b981;
            --brand-dark: #059669;
            --bg: #f8fafc;
            --card: #ffffff;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --radius: 20px;
        }

        body, .page-container { font-family: "Plus Jakarta Sans", sans-serif; background-color: var(--bg); height: 100vh; overflow: hidden; display: flex; flex-direction: column; }

        /* ===== MAP CONTAINER (FULL SCREEN FEEL) ===== */
        .map-wrapper {
            flex: 1; position: relative; width: 100%; height: 100%; z-index: 1;
        }
        #map { width: 100%; height: 100%; z-index: 1; }

        /* ===== FLOATING HEADER (OVERLAY) ===== */
        .floating-header {
            position: absolute; top: 20px; left: 20px; right: 20px; z-index: 1000;
            display: flex; justify-content: space-between; align-items: flex-start; pointer-events: none;
        }
        .header-card {
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            padding: 16px 20px; border-radius: 16px; pointer-events: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid rgba(255,255,255,0.5);
            max-width: 400px;
        }
        .header-title { font-size: 1.1rem; font-weight: 800; color: var(--ink); display: flex; align-items: center; gap: 8px; margin: 0; }
        .header-subtitle { font-size: 0.8rem; color: var(--muted); margin-top: 4px; font-weight: 600; }

        /* ===== FLOATING CONTROLS (RIGHT) ===== */
        .control-group {
            display: flex; flex-direction: column; gap: 10px; pointer-events: auto;
        }
        .btn-float {
            width: 44px; height: 44px; border-radius: 12px; background: #fff; border: 1px solid var(--line);
            color: var(--ink); display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); cursor: pointer; transition: 0.2s; font-size: 1.1rem;
        }
        .btn-float:hover { transform: scale(1.05); color: var(--brand); border-color: var(--brand); }
        .btn-float.active { background: var(--brand); color: #fff; border-color: var(--brand); }

        /* ===== FILTER BAR (BOTTOM CENTER) ===== */
        .floating-filter {
            position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); z-index: 1000;
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            padding: 8px; border-radius: 50px; display: flex; gap: 6px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,0.5);
            pointer-events: auto; width: max-content; max-width: 90%; overflow-x: auto;
        }
        .filter-chip {
            padding: 8px 16px; border-radius: 50px; border: 1px solid transparent; background: transparent;
            font-size: 0.8rem; font-weight: 700; color: var(--muted); cursor: pointer; white-space: nowrap; transition: 0.2s;
        }
        .filter-chip:hover { background: #f1f5f9; }
        .filter-chip.active { background: var(--brand); color: #fff; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }

        .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }

        /* ===== LEAFLET CUSTOM ===== */
        .leaflet-popup-content-wrapper { border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); font-family: "Plus Jakarta Sans", sans-serif; }
        .leaflet-popup-content { margin: 14px; }

        /* Status Indicator Pulse */
        .live-indicator {
            display: inline-block; width: 8px; height: 8px; background-color: #ef4444; border-radius: 50%;
            animation: pulse-red 2s infinite; margin-right: 6px;
        }
        @keyframes pulse-red {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
    </style>
@endpush

@section('content')
<div class="page-container">

    <div class="map-wrapper">
        <div id="map"></div>

        {{-- TOP LEFT: INFO --}}
        <div class="floating-header">
            <div class="header-card">
                <h1 class="header-title">
                    <i class="fa-solid fa-map-location-dot" style="color:var(--brand)"></i> Peta Jemputan
                </h1>
                <div class="header-subtitle">
                    <span class="live-indicator"></span> Monitoring realtime posisi armada & status jemputan.
                </div>
                <div style="margin-top:8px; font-size:0.75rem; color:var(--muted); font-weight:600;" id="infoText">
                    <i class="fa-solid fa-spinner fa-spin"></i> Menghubungkan ke satelit...
                </div>
            </div>

            {{-- TOP RIGHT: ACTIONS --}}
            <div class="control-group">
                <a href="{{ route('user.dashboard') }}" class="btn-float" title="Dashboard">
                    <i class="fa-solid fa-house"></i>
                </a>
                <a href="{{ route('user.setoran.index') }}" class="btn-float" title="Riwayat">
                    <i class="fa-solid fa-list"></i>
                </a>
                <button class="btn-float" onclick="map.setView([0.5071, 101.4478], 13)" title="Reset View">
                    <i class="fa-solid fa-compress"></i>
                </button>
            </div>
        </div>

        {{-- BOTTOM CENTER: FILTER --}}
        <div class="floating-filter" id="filterContainer">
            <button class="filter-chip active" onclick="setFilter('__all__', this)">Semua</button>
            <button class="filter-chip" onclick="setFilter('pending', this)"><span class="dot" style="background:#f59e0b"></span> Menunggu</button>
            <button class="filter-chip" onclick="setFilter('diambil', this)"><span class="dot" style="background:#3b82f6"></span> Diambil</button>
            <button class="filter-chip" onclick="setFilter('selesai', this)"><span class="dot" style="background:#94a3b8"></span> Selesai</button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // --- CONFIG & STATE ---
    const dataUrl = "{{ route('user.map.data') }}";
    const detailUrlBase = "{{ url('/user/setoran') }}";
    let activeFilter = '__all__';

    // --- MAP INIT ---
    const map = L.map('map', { zoomControl: false }).setView([0.5071, 101.4478], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OSM' }).addTo(map);
    L.control.zoom({ position: 'bottomright' }).addTo(map); // Pindah zoom control ke bawah kanan

    // --- ASSETS ---
    const truckIcon = L.divIcon({
        html: `<div style="background:#fff; border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 12px rgba(0,0,0,0.2); font-size:1.2rem;">🚛</div>`,
        className: "", iconSize: [36,36], iconAnchor:[18,18]
    });

    function circleIcon(color){
        return L.divIcon({
            className: "",
            html: `<div style="width:14px;height:14px;border-radius:50%;background:${color};border:2px solid #fff;box-shadow:0 0 0 4px ${color}40;"></div>`,
            iconSize:[14,14], iconAnchor:[7,7]
        });
    }

    const colorMap = {
        'pending': '#f59e0b', 'diambil': '#3b82f6', 'selesai': '#94a3b8', 'ditolak': '#ef4444', 'default': '#10b981'
    };

    // --- STATE STORAGE ---
    const layers = { pickup: new Map(), truck: new Map(), route: new Map() };
    const cache = { route: new Map() };

    // --- HELPER FUNCTIONS ---
    function setFilter(val, btn) {
        activeFilter = val;
        document.querySelectorAll('.filter-chip').forEach(el => el.classList.remove('active'));
        btn.classList.add('active');
        refresh(); // Trigger refresh manual
    }

    function shouldShow(status) {
        if(activeFilter === '__all__') return true;
        return String(status||'').toLowerCase() === activeFilter;
    }

    function fmtEta(seconds){
        const m = Math.round(seconds/60);
        if(m < 60) return m + " mnt";
        return Math.floor(m/60) + "j " + (m%60) + "m";
    }

    // --- POPUP CONTENT ---
    function getPopupContent(it, routeInfo) {
        const color = colorMap[String(it.status).toLowerCase()] || colorMap.default;
        const truckBadge = routeInfo
            ? `<div style="margin-top:8px; padding:8px; background:#f8fafc; border-radius:8px; font-size:0.75rem;">
                 <div style="font-weight:700; color:#0f172a;">🚛 ${it.petugas_name || 'Petugas'}</div>
                 <div style="display:flex; justify-content:space-between; margin-top:4px;">
                    <span>⏳ ${routeInfo.eta}</span> <span>📏 ${routeInfo.dist}</span>
                 </div>
               </div>`
            : '';

        return `
            <div style="min-width:200px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <span style="background:${color}20; color:${color}; padding:2px 8px; border-radius:4px; font-size:0.7rem; font-weight:800; text-transform:uppercase;">${it.status}</span>
                    <a href="${detailUrlBase}/${it.id}" style="font-size:0.75rem; font-weight:700; color:#64748b; text-decoration:none;">Detail ></a>
                </div>
                <div style="font-weight:700; color:#0f172a; font-size:0.9rem; margin-bottom:4px;">${it.alamat || 'Alamat tidak tersedia'}</div>
                <div style="font-size:0.7rem; color:#94a3b8;">ID: #${it.id}</div>
                ${truckBadge}
            </div>
        `;
    }

    // --- ROUTING LOGIC (OSRM) ---
    async function fetchRoute(start, end, id) {
        // Simple throttling: Don't fetch if update < 5s ago
        const now = Date.now();
        const last = cache.route.get(id);
        if(last && (now - last.time < 5000)) return last.data;

        try {
            const url = `https://router.project-osrm.org/route/v1/driving/${start.lng},${start.lat};${end.lng},${end.lat}?overview=full&geometries=geojson`;
            const res = await fetch(url);
            const json = await res.json();

            if(!json.routes || !json.routes[0]) return null;

            const r = json.routes[0];
            const result = {
                coords: r.geometry.coordinates.map(c => [c[1], c[0]]),
                info: { eta: fmtEta(r.duration), dist: (r.distance/1000).toFixed(1)+' km' }
            };

            cache.route.set(id, { time: now, data: result });
            return result;
        } catch(e) { return null; }
    }

    // --- MAIN REFRESH LOOP ---
    async function refresh() {
        try {
            const res = await fetch(dataUrl);
            if(!res.ok) return;
            const data = await res.json();
            const items = data.items || [];

            document.getElementById('infoText').innerHTML = `Aktif: <b>${items.length} titik</b> • Update: ${new Date().toLocaleTimeString()}`;

            const activeIds = new Set();

            for(const it of items) {
                if(!shouldShow(it.status)) continue;
                activeIds.add(it.id);

                // 1. PICKUP MARKER
                const color = colorMap[String(it.status).toLowerCase()] || colorMap.default;
                let marker = layers.pickup.get(it.id);
                if(!marker) {
                    marker = L.marker([it.lat, it.lng], { icon: circleIcon(color) }).addTo(map);
                    layers.pickup.set(it.id, marker);
                } else {
                    marker.setLatLng([it.lat, it.lng]).setIcon(circleIcon(color));
                }

                // 2. TRUCK & ROUTE LOGIC
                const hasTruck = (it.petugas_lat && it.petugas_lng);
                let routeInfo = null;

                if(hasTruck) {
                    const truckPos = { lat: Number(it.petugas_lat), lng: Number(it.petugas_lng) };

                    // Truck Marker
                    let truck = layers.truck.get(it.id);
                    if(!truck) {
                        truck = L.marker(truckPos, { icon: truckIcon, zIndexOffset: 1000 }).addTo(map);
                        layers.truck.set(it.id, truck);
                    } else {
                        // Simple animation logic can be added here
                        truck.setLatLng(truckPos);
                    }

                    // Draw Route
                    const routeData = await fetchRoute(truckPos, {lat: it.lat, lng: it.lng}, it.id);
                    if(routeData) {
                        routeInfo = routeData.info;
                        let line = layers.route.get(it.id);
                        if(!line) {
                            line = L.polyline(routeData.coords, { color: color, weight: 4, opacity: 0.8, dashArray: '10, 10' }).addTo(map);
                            layers.route.set(it.id, line);
                        } else {
                            line.setLatLngs(routeData.coords).setStyle({ color: color });
                        }
                    }
                } else {
                    // Cleanup truck/route if tracking stopped
                    if(layers.truck.has(it.id)) { map.removeLayer(layers.truck.get(it.id)); layers.truck.delete(it.id); }
                    if(layers.route.has(it.id)) { map.removeLayer(layers.route.get(it.id)); layers.route.delete(it.id); }
                }

                // Update Popup Content
                if(marker.getPopup() && marker.getPopup().isOpen()) {
                    marker.setPopupContent(getPopupContent(it, routeInfo));
                } else {
                    marker.bindPopup(getPopupContent(it, routeInfo));
                }
            }

            // Cleanup removed items
            ['pickup', 'truck', 'route'].forEach(type => {
                for(const [id, layer] of layers[type]) {
                    if(!activeIds.has(id)) {
                        map.removeLayer(layer);
                        layers[type].delete(id);
                    }
                }
            });

        } catch(e) { console.error("Map sync error", e); }
    }

    // Init & Interval
    refresh();
    setInterval(refresh, 4000); // 4 detik agar tidak spamming OSRM
</script>
@endpush
