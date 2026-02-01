@extends('layouts.petugas')

@section('title', 'Peta Jemputan')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>

    <style>
        /* ===== MAP CONTAINER (FULL SCREEN) ===== */
        .map-layout {
            position: relative; height: calc(100vh - 70px); width: 100%;
            overflow: hidden; background: #e5e7eb;
        }
        #map { height: 100%; width: 100%; z-index: 1; }

        /* ===== FLOATING HUD ===== */
        .hud-panel {
            position: absolute; z-index: 1000;
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(8px);
            border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.5); transition: 0.3s;
        }

        /* Top Left: Title */
        .hud-info {
            top: 20px; left: 20px; padding: 12px 16px; min-width: 200px;
        }
        .hud-title { font-size: 1rem; font-weight: 800; color: #111827; margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
        .hud-sub { font-size: 0.75rem; color: #6b7280; font-weight: 600; }
        .pulse-dot {
            width: 8px; height: 8px; background: #22c55e; border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); animation: pulse-green 2s infinite;
        }

        /* Top Right: Filter */
        .hud-filter {
            top: 20px; right: 20px; padding: 8px; display: flex; gap: 8px;
        }
        .filter-btn {
            padding: 8px 12px; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff;
            font-size: 0.8rem; font-weight: 700; color: #64748b; cursor: pointer; white-space: nowrap;
        }
        .filter-btn.active { background: #22c55e; color: #fff; border-color: #22c55e; }

        /* Bottom Center: Legend */
        .hud-legend {
            bottom: 30px; left: 50%; transform: translateX(-50%);
            padding: 8px 16px; border-radius: 50px; display: flex; gap: 16px; align-items: center;
        }
        .legend-item { display: flex; align-items: center; gap: 6px; font-size: 0.75rem; font-weight: 700; color: #64748b; }
        .dot { width: 10px; height: 10px; border-radius: 50%; border: 2px solid #fff; }

        /* Sidebar Detail (Slide-in) */
        .map-sidebar {
            position: absolute; bottom: 0; left: 0; right: 0; z-index: 1001;
            background: #fff; border-radius: 20px 20px 0 0;
            box-shadow: 0 -5px 25px rgba(0,0,0,0.1);
            transform: translateY(100%); transition: transform 0.3s ease;
            max-height: 60%; display: flex; flex-direction: column;
        }
        .map-sidebar.active { transform: translateY(0); }

        .sidebar-header {
            padding: 16px 20px; border-bottom: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; align-items: center;
        }
        .sidebar-body { padding: 20px; overflow-y: auto; }

        .btn-close { background: none; border: none; font-size: 1.2rem; color: #94a3b8; }

        .btn-action {
            display: block; width: 100%; text-align: center; padding: 12px;
            background: #22c55e; color: #fff; font-weight: 700; border-radius: 10px;
            text-decoration: none; margin-top: 16px;
        }

        @keyframes pulse-green {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        @media (min-width: 768px) {
            .map-sidebar {
                top: 20px; right: 20px; bottom: 20px; left: auto; width: 350px;
                border-radius: 16px; transform: translateX(120%); max-height: none;
            }
            .map-sidebar.active { transform: translateX(0); }
        }
    </style>
@endpush

@section('content')
<div class="map-layout">
    <div id="map"></div>

    {{-- HUD Info --}}
    <div class="hud-panel hud-info">
        <div class="hud-title">
            <div class="pulse-dot"></div> Peta Jemputan
        </div>
        <div class="hud-sub" id="infoText">Memuat data...</div>
    </div>

    {{-- HUD Filter --}}
    <div class="hud-panel hud-filter">
        <button class="filter-btn active" onclick="setFilter('__all__', this)">Semua</button>
        <button class="filter-btn" onclick="setFilter('pending', this)">Baru</button>
        <button class="filter-btn" onclick="setFilter('assigned', this)">Tugas Saya</button>
    </div>

    {{-- HUD Legend --}}
    <div class="hud-panel hud-legend">
        <div class="legend-item"><div class="dot" style="background:#22c55e; width:14px; height:14px;"></div> Tugas Saya</div>
        <div class="legend-item"><div class="dot" style="background:#f59e0b"></div> Pending</div>
        <div class="legend-item"><div class="dot" style="background:#3b82f6"></div> Proses</div>
    </div>

    {{-- Sidebar Detail --}}
    <div class="map-sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4 style="margin:0; font-weight:800; color:#111827;">Detail Lokasi</h4>
            <button class="btn-close" onclick="closeSidebar()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="sidebar-body" id="sidebarContent">
            </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const dataUrl = "{{ route('petugas.map.data') }}";
    const detailUrlBase = "{{ url('/petugas/setoran') }}";

    // Map Init
    const map = L.map('map', { zoomControl: false }).setView([0.5071, 101.4478], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
    L.control.zoom({ position: 'bottomright' }).addTo(map);

    let pickupMarkers = new Map();
    let currentFilter = '__all__';

    // Icons
    function getIcon(status, isMine) {
        let color = '#94a3b8'; // Default grey
        let size = 14;
        let zIndex = 100;

        if (isMine) { color = '#22c55e'; size = 20; zIndex = 500; }
        else if (status === 'pending') { color = '#f59e0b'; }
        else if (status === 'diproses' || status === 'diambil') { color = '#3b82f6'; }

        return L.divIcon({
            className: '',
            html: `<div style="background:${color}; width:${size}px; height:${size}px; border-radius:50%; border:3px solid #fff; box-shadow:0 4px 10px rgba(0,0,0,0.2);"></div>`,
            iconSize: [size, size], iconAnchor: [size/2, size/2]
        });
    }

    // Logic
    function setFilter(val, btn) {
        currentFilter = val;
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        refresh();
    }

    async function refresh() {
        try {
            const res = await fetch(dataUrl);
            const data = await res.json();
            const items = data.items || [];

            document.getElementById('infoText').innerText = `Total: ${items.length} titik ditemukan`;

            const activeIds = new Set();

            items.forEach(it => {
                const isMine = it.is_assigned_to_me;
                const status = String(it.status).toLowerCase();

                // Filter Logic
                if (currentFilter === 'assigned' && !isMine) return;
                if (currentFilter === 'pending' && status !== 'pending') return;

                activeIds.add(it.id);

                if (!pickupMarkers.has(it.id)) {
                    const m = L.marker([it.lat, it.lng], {
                        icon: getIcon(status, isMine),
                        zIndexOffset: isMine ? 1000 : 100
                    }).addTo(map);

                    m.on('click', () => openSidebar(it));
                    pickupMarkers.set(it.id, m);
                } else {
                    pickupMarkers.get(it.id).setLatLng([it.lat, it.lng]);
                }
            });

            // Cleanup
            pickupMarkers.forEach((m, id) => {
                if (!activeIds.has(id)) {
                    map.removeLayer(m);
                    pickupMarkers.delete(id);
                }
            });

        } catch (e) { console.error(e); }
    }

    function openSidebar(it) {
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('sidebarContent');
        const isMine = it.is_assigned_to_me;

        let statusBadge = `<span style="background:#f3f4f6; color:#64748b; padding:4px 8px; border-radius:6px; font-size:0.75rem; font-weight:700; text-transform:uppercase;">${it.status}</span>`;

        if(isMine) statusBadge = `<span style="background:#dcfce7; color:#166534; padding:4px 8px; border-radius:6px; font-size:0.75rem; font-weight:700; text-transform:uppercase;">TUGAS SAYA</span>`;

        content.innerHTML = `
            <div style="margin-bottom:12px;">${statusBadge}</div>
            <h3 style="margin:0 0 4px; font-size:1.1rem; color:#111827;">${it.user_name}</h3>
            <p style="margin:0 0 16px; color:#6b7280; font-size:0.9rem;">${it.alamat || 'Alamat tidak tersedia'}</p>

            <div style="background:#f9fafb; padding:12px; border-radius:8px; font-size:0.85rem; color:#374151;">
                <strong>Koordinat:</strong><br>
                ${it.lat.toFixed(6)}, ${it.lng.toFixed(6)}
            </div>

            <a href="${detailUrlBase}/${it.id}" class="btn-action">
                Lihat Detail & Aksi
            </a>

            <a href="https://www.google.com/maps/dir/?api=1&destination=${it.lat},${it.lng}" target="_blank"
               style="display:block; text-align:center; margin-top:10px; color:#3b82f6; text-decoration:none; font-weight:600; font-size:0.9rem;">
               <i class="fa-solid fa-location-arrow"></i> Navigasi Google Maps
            </a>
        `;
        sidebar.classList.add('active');
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('active');
    }

    // Init
    refresh();
    setInterval(refresh, 5000);
</script>
@endpush
