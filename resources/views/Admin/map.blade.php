@extends('layouts.admin')

@section('title', 'Live Monitoring')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

    <style>
        :root {
            --brand: #10b981;
            --brand-dark: #059669;
            --bg: #f8fafc;
            --card: #ffffff;
            --ink: #0f172a;
            --muted: #64748b;
        }

        /* Hilangkan padding default container agar peta full */
        .container-fluid { padding: 0 !important; max-width: none !important; margin: 0 !important; }

        /* Layout Utama */
        .map-layout {
            position: relative;
            height: calc(100vh - 70px); /* Sesuaikan dengan tinggi navbar admin */
            width: 100%;
            overflow: hidden;
            display: flex;
        }

        #map {
            flex: 1;
            height: 100%;
            width: 100%;
            z-index: 1;
            background: #e5e7eb;
        }

        /* ===== FLOATING HUD (OVERLAYS) ===== */
        .hud-panel {
            position: absolute;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.5);
            transition: 0.3s;
        }

        /* Top Left: Title & Stats */
        .hud-stats {
            top: 20px; left: 20px;
            padding: 16px 20px;
            min-width: 280px;
        }
        .hud-title {
            font-size: 1.1rem; font-weight: 800; color: var(--ink);
            display: flex; align-items: center; gap: 8px; margin-bottom: 12px;
        }
        .live-dot {
            width: 10px; height: 10px; background: #ef4444; border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            animation: pulse-red 2s infinite;
        }

        .stats-row { display: flex; gap: 16px; }
        .stat-bit { flex: 1; }
        .stat-bit label { display: block; font-size: 0.7rem; color: var(--muted); text-transform: uppercase; font-weight: 700; }
        .stat-bit val { display: block; font-size: 1.2rem; font-weight: 800; color: var(--brand-dark); }

        /* Top Right: Filters */
        .hud-filter {
            top: 20px; right: 20px;
            padding: 12px;
            display: flex; gap: 8px;
        }
        .filter-select {
            padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px;
            font-size: 0.85rem; font-weight: 600; color: var(--ink); outline: none;
            cursor: pointer; background: #fff;
        }
        .btn-refresh {
            width: 36px; height: 36px; display: grid; place-items: center;
            border-radius: 8px; background: var(--brand); color: #fff; border: none;
            cursor: pointer; transition: 0.2s;
        }
        .btn-refresh:hover { background: var(--brand-dark); transform: rotate(180deg); }

        /* Bottom Center: Legend */
        .hud-legend {
            bottom: 30px; left: 50%; transform: translateX(-50%);
            padding: 8px 16px; border-radius: 50px;
            display: flex; gap: 16px; align-items: center;
        }
        .legend-item { display: flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 700; color: var(--muted); }
        .dot { width: 10px; height: 10px; border-radius: 50%; border: 2px solid #fff; }

        /* Sidebar Detail (Slide-in) */
        .map-sidebar {
            position: absolute; top: 0; right: 0; bottom: 0;
            width: 350px; background: #fff; z-index: 1001;
            box-shadow: -5px 0 25px rgba(0,0,0,0.1);
            transform: translateX(100%); transition: transform 0.3s ease;
            display: flex; flex-direction: column;
        }
        .map-sidebar.active { transform: translateX(0); }

        .sidebar-header {
            padding: 20px; border-bottom: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; align-items: center;
            background: var(--bg);
        }
        .sidebar-body { flex: 1; overflow-y: auto; padding: 20px; }
        .sidebar-footer { padding: 16px; border-top: 1px solid #e2e8f0; }

        .btn-close-sidebar { background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--muted); }

        /* Custom Markers */
        .truck-pulse {
            width: 30px; height: 30px; background: #3b82f6; border: 2px solid #fff;
            border-radius: 50%; display: grid; place-items: center; color: #fff;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.4);
        }

        @keyframes pulse-red {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .hud-stats { left: 10px; right: 10px; width: auto; top: 10px; }
            .hud-filter { top: 110px; left: 10px; right: 10px; width: auto; flex-wrap: wrap; justify-content: center; }
            .hud-legend { display: none; } /* Hide legend on mobile to save space */
            .map-sidebar { width: 100%; }
        }
    </style>
@endpush

@section('content')
<div class="map-layout">

    <div id="map"></div>

    {{-- HUD 1: Title & Stats --}}
    <div class="hud-panel hud-stats">
        <div class="hud-title">
            <div class="live-dot"></div>
            <span>Monitoring Setoran</span>
        </div>
        <div class="stats-row">
            <div class="stat-bit">
                <label>Total Aktif</label>
                <val id="countTotal">-</val>
            </div>
            <div class="stat-bit">
                <label>Menunggu</label>
                <val id="countPending" style="color:#f59e0b">-</val>
            </div>
            <div class="stat-bit">
                <label>Petugas</label>
                <val id="countTruck" style="color:#3b82f6">-</val>
            </div>
        </div>
    </div>

    {{-- HUD 2: Filters --}}
    <div class="hud-panel hud-filter">
        <select id="filterStatus" class="filter-select">
            <option value="all">Semua Status</option>
            <option value="menunggu">Menunggu</option>
            <option value="diproses">Sedang Dijemput</option>
            <option value="selesai">Selesai</option>
        </select>
        <select id="filterType" class="filter-select">
            <option value="all">Semua Tipe</option>
            <option value="jemput">Jemput</option>
            <option value="antar">Antar</option>
        </select>
        <button class="btn-refresh" onclick="fetchData()" title="Refresh Data">
            <i class="fa-solid fa-rotate"></i>
        </button>
    </div>

    {{-- HUD 3: Legend --}}
    <div class="hud-panel hud-legend">
        <div class="legend-item"><div class="dot" style="background:#f59e0b"></div> Menunggu</div>
        <div class="legend-item"><div class="dot" style="background:#3b82f6"></div> Proses</div>
        <div class="legend-item"><div class="dot" style="background:#10b981"></div> Selesai</div>
        <div class="legend-item"><div class="dot" style="background:#ef4444"></div> Batal</div>
        <div class="legend-item">
            <i class="fa-solid fa-truck" style="color:#3b82f6; margin-right:4px;"></i> Petugas
        </div>
    </div>

    {{-- SIDEBAR DETAIL --}}
    <div class="map-sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3 style="margin:0; font-size:1.1rem; font-weight:800;">Detail Setoran</h3>
            <button class="btn-close-sidebar" onclick="closeSidebar()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="sidebar-body" id="sidebarContent">
            <div style="text-align:center; color:#94a3b8; margin-top:50px;">
                <i class="fa-regular fa-map" style="font-size:3rem; margin-bottom:10px;"></i>
                <p>Klik marker pada peta untuk melihat detail.</p>
            </div>
        </div>
        <div class="sidebar-footer" id="sidebarFooter" style="display:none;">
            <a href="#" id="btnDetailLink" class="btn btn-primary w-100" style="display:block; text-align:center; padding:10px; background:#10b981; color:#fff; border-radius:8px; text-decoration:none; font-weight:700;">
                Lihat Detail Lengkap
            </a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
    const DATA_URL = "{{ route('admin.map.data') }}";

    // Init Map
    const map = L.map('map', { zoomControl: false }).setView([-6.2000, 106.8166], 13); // Default view
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OSM' }).addTo(map);
    L.control.zoom({ position: 'bottomright' }).addTo(map);

    // Layer Groups
    const markersCluster = L.markerClusterGroup({
        showCoverageOnHover: false,
        maxClusterRadius: 40
    });
    const truckLayer = L.layerGroup();
    map.addLayer(markersCluster);
    map.addLayer(truckLayer);

    // Data Cache
    let pickupMarkers = {}; // id -> marker
    let truckMarkers = {};  // id -> marker

    // Colors
    const colors = {
        'menunggu': '#f59e0b',
        'diproses': '#3b82f6',
        'dijemput': '#3b82f6',
        'selesai': '#10b981',
        'ditolak': '#ef4444',
        'dibatalkan': '#ef4444',
        'default': '#94a3b8'
    };

    function getIcon(status) {
        const c = colors[status.toLowerCase()] || colors.default;
        return L.divIcon({
            className: 'custom-pin',
            html: `<div style="background:${c}; width:20px; height:20px; border-radius:50%; border:3px solid #fff; box-shadow:0 3px 8px rgba(0,0,0,0.2);"></div>`,
            iconSize: [20, 20], iconAnchor: [10, 10]
        });
    }

    const truckIcon = L.divIcon({
        className: '',
        html: `<div class="truck-pulse"><i class="fa-solid fa-truck" style="font-size:0.8rem"></i></div>`,
        iconSize: [30, 30], iconAnchor: [15, 15]
    });

    // --- LOGIC FETCH DATA ---
    async function fetchData() {
        try {
            const res = await fetch(DATA_URL);
            const json = await res.json();
            const items = json.items || [];

            updateMap(items);
            updateStats(items);
        } catch (e) { console.error("Map fetch error:", e); }
    }

    function updateMap(items) {
        const filterStatus = document.getElementById('filterStatus').value;
        const filterType = document.getElementById('filterType').value;

        const activeIds = new Set();
        const truckIds = new Set();

        items.forEach(item => {
            // Apply Filters
            if(filterStatus !== 'all' && item.status.toLowerCase() !== filterStatus) return;
            if(filterType === 'jemput' && item.metode !== 'jemput') return; // Filter contoh logic (sesuaikan jika ada field metode di response)

            // 1. Pickup Marker
            activeIds.add(item.id);
            if (!pickupMarkers[item.id]) {
                const m = L.marker([item.lat, item.lng], { icon: getIcon(item.status) });
                m.on('click', () => openSidebar(item));
                markersCluster.addLayer(m);
                pickupMarkers[item.id] = m;
            } else {
                pickupMarkers[item.id].setLatLng([item.lat, item.lng]);
                pickupMarkers[item.id].setIcon(getIcon(item.status));
            }

            // 2. Truck Marker (Jika ada data petugas_lat)
            if (item.petugas_lat && item.petugas_lng) {
                truckIds.add(item.id);
                if (!truckMarkers[item.id]) {
                    const t = L.marker([item.petugas_lat, item.petugas_lng], { icon: truckIcon, zIndexOffset: 1000 }).addTo(map);
                    t.bindPopup(`<b>${item.petugas_name}</b><br>Menuju ID #${item.id}`);
                    truckLayer.addLayer(t);
                    truckMarkers[item.id] = t;
                } else {
                    truckMarkers[item.id].setLatLng([item.petugas_lat, item.petugas_lng]);
                }
            }
        });

        // Cleanup removed markers
        Object.keys(pickupMarkers).forEach(id => {
            if (!activeIds.has(parseInt(id))) {
                markersCluster.removeLayer(pickupMarkers[id]);
                delete pickupMarkers[id];
            }
        });
        Object.keys(truckMarkers).forEach(id => {
            if (!truckIds.has(parseInt(id))) {
                truckLayer.removeLayer(truckMarkers[id]);
                delete truckMarkers[id];
            }
        });
    }

    function updateStats(items) {
        document.getElementById('countTotal').innerText = items.length;
        document.getElementById('countPending').innerText = items.filter(i => i.status === 'menunggu').length;
        document.getElementById('countTruck').innerText = items.filter(i => i.petugas_lat).length;
    }

    // --- SIDEBAR UI ---
    function openSidebar(item) {
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('sidebarContent');
        const footer = document.getElementById('sidebarFooter');
        const btnLink = document.getElementById('btnDetailLink');

        const color = colors[item.status.toLowerCase()] || '#ccc';

        let html = `
            <div style="margin-bottom:20px;">
                <span style="background:${color}20; color:${color}; padding:4px 10px; border-radius:6px; font-weight:700; font-size:0.8rem; text-transform:uppercase;">
                    ${item.status}
                </span>
                <h2 style="margin:10px 0 5px; color:var(--ink);">Setoran #${item.id}</h2>
                <div style="font-size:0.9rem; color:var(--muted);"><i class="fa-regular fa-clock"></i> ${item.created_at}</div>
            </div>

            <div style="background:#f8fafc; padding:15px; border-radius:10px; border:1px solid #e2e8f0; margin-bottom:20px;">
                <label style="font-size:0.75rem; color:var(--muted); font-weight:700;">NASABAH</label>
                <div style="font-weight:700; color:var(--ink);">${item.user_name}</div>

                <hr style="margin:10px 0; border:0; border-top:1px dashed #cbd5e1;">

                <label style="font-size:0.75rem; color:var(--muted); font-weight:700;">LOKASI JEMPUT</label>
                <div style="font-size:0.9rem; line-height:1.4;">${item.alamat || '-'}</div>
            </div>
        `;

        if(item.petugas_id) {
            html += `
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
                    <div style="width:40px; height:40px; background:#eff6ff; color:#3b82f6; border-radius:50%; display:grid; place-items:center;">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;">${item.petugas_name}</div>
                        <div style="font-size:0.8rem; color:var(--muted);">Last seen: ${item.petugas_last_seen || 'N/A'}</div>
                    </div>
                </div>
            `;
        }

        content.innerHTML = html;
        btnLink.href = `/admin/setoran/${item.id}`; // Sesuaikan route detail admin
        footer.style.display = 'block';
        sidebar.classList.add('active');
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('active');
    }

    // --- EVENTS ---
    document.getElementById('filterStatus').addEventListener('change', fetchData);
    document.getElementById('filterType').addEventListener('change', fetchData);

    // Initial Load
    fetchData();
    setInterval(fetchData, 5000); // Polling 5 detik
</script>
@endpush
