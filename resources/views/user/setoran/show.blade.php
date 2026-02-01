@extends('layouts.user')
@section('title', 'Detail Setoran')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <style>
        /* ===== ROOT VARS (Konsisten dengan Index) ===== */
        :root {
            --brand: #10b981;
            --brand-dark: #059669;
            --brand-soft: #ecfdf5;
            --bg: #f8fafc;
            --card: #ffffff;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --radius: 20px;
        }

        body {
            font-family: "Plus Jakarta Sans", sans-serif;
            background-color: var(--bg);
        }

        .container-fluid {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 16px;
        }

        @media (min-width:768px) {
            .container-fluid {
                padding: 0 32px;
            }
        }

        /* ===== HERO HEADER ===== */
        .page-header {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            padding: 30px 0 70px;
            color: #fff;
            border-radius: 0 0 50px 50px;
            margin-bottom: -50px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px -10px rgba(16, 185, 129, 0.5);
            z-index: 1;
        }

        .header-content {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .title-group h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .title-group p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 8px 16px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            backdrop-filter: blur(5px);
            transition: .2s;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* ===== LAYOUT GRID ===== */
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            position: relative;
            z-index: 10;
        }

        @media (max-width: 860px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.8);
            overflow: hidden;
        }

        .card-head {
            padding: 16px 20px;
            border-bottom: 1px solid var(--line);
            background: #fff;
            font-weight: 800;
            color: var(--ink);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ===== ITEM LIST ===== */
        .item-list {
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .item-row {
            padding: 16px 20px;
            border-bottom: 1px dashed var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-info b {
            display: block;
            color: var(--ink);
            font-size: 0.95rem;
            margin-bottom: 2px;
        }

        .item-info span {
            color: var(--muted);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .item-price {
            font-weight: 800;
            color: var(--brand-dark);
            font-family: monospace;
            font-size: 1rem;
        }

        /* ===== STATUS & SUMMARY CARD ===== */
        .summary-content {
            padding: 20px;
        }

        .status-badge {
            display: block;
            text-align: center;
            padding: 12px;
            border-radius: 12px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .st-pending {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }

        .st-process {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }

        .st-success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #d1fae5;
        }

        .st-cancel {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fee2e2;
        }

        .summ-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: var(--muted);
            font-weight: 600;
        }

        .summ-row.total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed var(--line);
            color: var(--ink);
            font-size: 1.1rem;
            font-weight: 900;
        }

        /* ===== MAP TRACKING UI ===== */
        .tracking-card {
            margin-top: 20px;
            overflow: hidden;
            border: 2px solid var(--brand-soft);
        }

        #map {
            height: 400px;
            width: 100%;
            z-index: 1;
        }

        /* Live Status Bar floating on Map */
        .live-status {
            background: #fff;
            padding: 15px 20px;
            border-top: 1px solid var(--line);
        }

        .driver-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .driver-icon {
            width: 45px;
            height: 45px;
            background: var(--brand-soft);
            color: var(--brand-dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .driver-text h4 {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--ink);
        }

        .driver-text div {
            font-size: 0.8rem;
            color: var(--muted);
            font-weight: 600;
            margin-top: 2px;
        }

        /* KPI Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
        }

        .kpi-box {
            background: #f8fafc;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid var(--line);
        }

        .kpi-label {
            font-size: 0.7rem;
            color: var(--muted);
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .kpi-val {
            font-size: 0.9rem;
            font-weight: 900;
            color: var(--ink);
        }

        .btn-maps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 15px;
            padding: 12px;
            background: var(--brand);
            color: #fff;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 800;
            font-size: 0.9rem;
            transition: .2s;
        }

        .btn-maps:hover {
            background: var(--brand-dark);
        }
    </style>
@endpush

@section('content')

    {{-- HEADER --}}
    <div class="page-header">
        <div class="container-fluid">
            <div class="header-content">
                <div class="title-group">
                    <h2>📦 Detail Setoran</h2>
                    <p>ID Transaksi: #{{ $setoran->id }} • {{ $setoran->created_at->format('d M Y') }}</p>
                </div>
                <a class="btn-back" href="{{ route('user.setoran.index') }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="main-grid">

            {{-- KOLOM KIRI: ITEM & MAP --}}
            <div class="left-col">

                {{-- CARD ITEM --}}
                <div class="card">
                    <div class="card-head">
                        <span><i class="fa-solid fa-list-check"></i> Rincian Item</span>
                        <span style="font-size:0.8rem; color:var(--muted)">{{ $setoran->items->count() }} Jenis</span>
                    </div>
                    <ul class="item-list">
                        @foreach ($setoran->items as $d)
                            <li class="item-row">
                                <div class="item-info">
                                    <b>{{ $d->kategori->nama_sampah ?? 'Item dihapus' }}</b>
                                    <span>{{ $d->kategori->masterKategori->nama_kategori ?? '-' }} • {{ $d->jumlah }}
                                        {{ $d->satuan ?? 'kg' }}</span>
                                </div>
                                <div class="item-price">
                                    Rp {{ number_format($d->subtotal) }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- TRACKING CARD (Hanya jika Jemput) --}}
                @if ($setoran->metode === 'jemput' && $setoran->latitude)
                    <div class="card tracking-card">
                        <div class="card-head"
                            style="background:var(--brand-soft); color:var(--brand-dark); border-bottom-color:rgba(16,185,129,0.2)">
                            <span><i class="fa-solid fa-satellite-dish"></i> Live Tracking</span>
                            <span
                                style="font-size:0.75rem; background:#fff; padding:4px 8px; border-radius:6px;">Real-time</span>
                        </div>

                        <div id="map"></div>

                        <div class="live-status">
                            <div class="driver-info">
                                <div class="driver-icon"><i class="fa-solid fa-truck-fast"></i></div>
                                <div class="driver-text">
                                    <h4 id="petugasInfo">Menunggu armada...</h4>
                                    <div id="statusText">Status: {{ ucfirst($setoran->status) }}</div>
                                </div>
                            </div>

                            <div class="kpi-grid">
                                <div class="kpi-box">
                                    <div class="kpi-label">Estimasi Tiba</div>
                                    <div class="kpi-val" id="etaText">--</div>
                                </div>
                                <div class="kpi-box">
                                    <div class="kpi-label">Jarak</div>
                                    <div class="kpi-val" id="distText">--</div>
                                </div>
                                <div class="kpi-box">
                                    <div class="kpi-label">Terpantau</div>
                                    <div class="kpi-val" id="seenText">--</div>
                                </div>
                            </div>

                            <a href="https://www.google.com/maps?q={{ $setoran->latitude }},{{ $setoran->longitude }}"
                                target="_blank" class="btn-maps">
                                <i class="fa-solid fa-map-location-dot"></i> Buka Google Maps
                            </a>
                        </div>
                    </div>
                @endif

            </div>

            {{-- KOLOM KANAN: RINGKASAN --}}
            <div class="right-col">
                <div class="card summary-content">
                    @php
                        $status = strtolower($setoran->status);
                        $badgeClass = match ($status) {
                            'menunggu' => 'st-pending',
                            'proses', 'dijemput' => 'st-process',
                            'selesai' => 'st-success',
                            'batal' => 'st-cancel',
                            default => 'st-pending',
                        };
                    @endphp
                    <span class="status-badge {{ $badgeClass }}">
                        {{ $setoran->status }}
                    </span>

                    <div class="summ-row">
                        <span>Metode</span>
                        <span style="font-weight:800; color:var(--ink)">{{ ucfirst($setoran->metode) }}</span>
                    </div>
                    @if ($setoran->metode == 'jemput')
                        <div class="summ-row">
                            <span>Jadwal</span>
                            <span style="text-align:right; font-weight:800; color:var(--ink)">
                                {{ \Carbon\Carbon::parse($setoran->jadwal_jemput)->format('d M Y') }}<br>
                                <small
                                    style="color:var(--muted)">{{ \Carbon\Carbon::parse($setoran->jadwal_jemput)->format('H:i') }}
                                    WIB</small>
                            </span>
                        </div>
                    @endif

                    <div class="summ-row total">
                        <span>Total Estimasi</span>
                        <span style="color:var(--brand-dark)">Rp {{ number_format($setoran->estimasi_total) }}</span>
                    </div>

                    @if ($setoran->metode == 'jemput')
                        <div
                            style="margin-top:20px; padding:12px; background:#f8fafc; border-radius:12px; border:1px solid var(--line);">
                            <div style="font-size:0.75rem; color:var(--muted); font-weight:700; margin-bottom:4px;">ALAMAT
                                JEMPUT</div>
                            <div style="font-size:0.85rem; font-weight:600; line-height:1.4;">{{ $setoran->alamat }}</div>
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
        // ... (KODE JAVASCRIPT LAMA KAMU TETAP DISINI SAMA PERSIS) ...
        // ... HANYA PASTIKAN ID ELEMENT HTML DI ATAS MATCH DENGAN JS ...

        // Variabel PHP ke JS
        const tujuanLat = Number("{{ $setoran->latitude }}");
        const tujuanLng = Number("{{ $setoran->longitude }}");
        const urlLoc = "{{ route('user.setoran.petugas_location', $setoran->id) }}";

        const truckIcon = L.divIcon({
            html: "🚛",
            className: "",
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        }); // Sedikit diperbesar iconnya

        @if ($setoran->metode === 'jemput' && $setoran->latitude && $setoran->longitude)
            const map = L.map('map', {
                scrollWheelZoom: false
            }).setView([tujuanLat, tujuanLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            const tujuanMarker = L.marker([tujuanLat, tujuanLng]).addTo(map).bindPopup(
                "<b>Titik Jemput</b><br>{{ $setoran->alamat }}").openPopup();

            let petugasMarker = null;
            let routeOutline = null;
            let routeMain = null;
            let routeCoords = null;
            let lastKnownLat = null,
                lastKnownLng = null;

            // ... (Paste logic Haversine, SnapToRoute, AnimationFrame kamu disini) ...
            // ... Tidak ada perubahan logic, hanya pastikan ID seperti 'etaText', 'distText' ada di HTML baru ...

            function fmtKm(m) {
                return (m / 1000).toFixed(1) + " km";
            }

            function fmtEta(seconds) {
                const m = Math.round(seconds / 60);
                if (m < 60) return m + " mnt"; // Singkat
                const h = Math.floor(m / 60);
                const r = m % 60;
                return h + "j " + r + "m";
            }

            function haversineMeters(aLat, aLng, bLat, bLng) {
                const R = 6371000;
                const toRad = d => d * Math.PI / 180;
                const dLat = toRad(bLat - aLat),
                    dLng = toRad(bLng - aLng);
                const s1 = Math.sin(dLat / 2),
                    s2 = Math.sin(dLng / 2);
                const c = 2 * Math.asin(Math.sqrt(s1 * s1 + Math.cos(toRad(aLat)) * Math.cos(toRad(bLat)) * s2 * s2));
                return R * c;
            }

            function closestPointOnSegment(p, a, b) {
                const ax = a.lng,
                    ay = a.lat,
                    bx = b.lng,
                    by = b.lat,
                    px = p.lng,
                    py = p.lat;
                const abx = bx - ax,
                    aby = by - ay;
                const apx = px - ax,
                    apy = py - ay;
                const ab2 = abx * abx + aby * aby;
                const t = ab2 ? (apx * abx + apy * aby) / ab2 : 0;
                const tt = Math.max(0, Math.min(1, t));
                return {
                    lat: ay + aby * tt,
                    lng: ax + abx * tt
                };
            }

            function snapToRoute(lat, lng) {
                if (!routeCoords || routeCoords.length < 2) return {
                    lat,
                    lng
                };
                const p = {
                    lat,
                    lng
                };
                let best = null,
                    bestD = Infinity;
                for (let i = 0; i < routeCoords.length - 1; i++) {
                    const a = {
                        lat: routeCoords[i][0],
                        lng: routeCoords[i][1]
                    };
                    const b = {
                        lat: routeCoords[i + 1][0],
                        lng: routeCoords[i + 1][1]
                    };
                    const c = closestPointOnSegment(p, a, b);
                    const d = haversineMeters(p.lat, p.lng, c.lat, c.lng);
                    if (d < bestD) {
                        bestD = d;
                        best = c;
                    }
                }
                return best || {
                    lat,
                    lng
                };
            }

            let animFrom = null,
                animTo = null,
                animStart = 0,
                animDur = 700;

            function ensureMarker(lat, lng) {
                if (!petugasMarker) {
                    petugasMarker = L.marker([lat, lng], {
                        icon: truckIcon
                    }).addTo(map).bindPopup("🚛 Truk Petugas");
                }
            }

            function setTargetPosition(lat, lng) {
                ensureMarker(lat, lng);
                let targetLat = lat,
                    targetLng = lng;
                if (routeCoords && routeCoords.length > 0) {
                    const snapped = snapToRoute(lat, lng);
                    targetLat = snapped.lat;
                    targetLng = snapped.lng;
                }
                const cur = petugasMarker.getLatLng();
                animFrom = {
                    lat: cur.lat,
                    lng: cur.lng
                };
                animTo = {
                    lat: targetLat,
                    lng: targetLng
                };
                animStart = performance.now();
            }

            function tick() {
                if (petugasMarker && animFrom && animTo) {
                    const now = performance.now();
                    const t = Math.min(1, (now - animStart) / animDur);
                    const lat = animFrom.lat + (animTo.lat - animFrom.lat) * t;
                    const lng = animFrom.lng + (animTo.lng - animFrom.lng) * t;
                    petugasMarker.setLatLng([lat, lng]);
                }
                requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);

            function setRoute(coordsLatLng) {
                routeCoords = coordsLatLng;
                if (routeOutline) routeOutline.setLatLngs(coordsLatLng);
                else routeOutline = L.polyline(coordsLatLng, {
                    weight: 11,
                    opacity: 0.35,
                    color: '#10b981'
                }).addTo(map);

                if (routeMain) routeMain.setLatLngs(coordsLatLng);
                else routeMain = L.polyline(coordsLatLng, {
                    weight: 6,
                    opacity: 0.9,
                    color: '#047857'
                }).addTo(map);
            }

            let lastRouteAt = 0;
            let lastRouteFromLat = null,
                lastRouteFromLng = null;

            async function drawRoute(fromLat, fromLng) {
                const url =
                    `https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${tujuanLng},${tujuanLat}?overview=full&geometries=geojson`;
                try {
                    const res = await fetch(url, {
                        cache: "no-store"
                    });
                    const data = await res.json();
                    if (!data.routes || !data.routes[0]) return;

                    const r = data.routes[0];
                    const coords = r.geometry.coordinates.map(c => [c[1], c[0]]);
                    setRoute(coords);

                    document.getElementById('etaText').innerText = fmtEta(r.duration);
                    document.getElementById('distText').innerText = fmtKm(r.distance);
                } catch (e) {}
            }

            async function refreshPetugas() {
                try {
                    const res = await fetch(urlLoc, {
                        cache: "no-store"
                    });
                    if (!res.ok) return;
                    const data = await res.json();

                    // Update Text Status jika berubah
                    // if(data.status) document.getElementById('statusText').innerText = ... (Optional)

                    const info = document.getElementById('petugasInfo');
                    const seenText = document.getElementById('seenText');

                    if (!data.petugas_id) {
                        info.innerText = 'Menunggu armada...';
                        seenText.innerText = '-';
                        if (petugasMarker) {
                            map.removeLayer(petugasMarker);
                            petugasMarker = null;
                        }
                        return;
                    }

                    const name = data.petugas_name || 'Petugas';
                    // Format last seen simple
                    seenText.innerText = data.petugas_last_seen ? data.petugas_last_seen.split(' ')[1] :
                    '-'; // Ambil jam saja

                    const hasLatLng = (data.petugas_latitude && data.petugas_longitude);

                    if (hasLatLng) {
                        const lat = Number(data.petugas_latitude);
                        const lng = Number(data.petugas_longitude);

                        lastKnownLat = lat;
                        lastKnownLng = lng;
                        ensureMarker(lat, lng);
                        setTargetPosition(lat, lng);
                        info.innerText = `${name} menuju lokasi`;

                        const now = Date.now();
                        const moved = (lastRouteFromLat !== null) ? haversineMeters(lastRouteFromLat, lastRouteFromLng,
                            lat, lng) : 9999;

                        if (!routeCoords || (now - lastRouteAt) >= 4000 || moved >= 15) {
                            lastRouteAt = now;
                            lastRouteFromLat = lat;
                            lastRouteFromLng = lng;
                            await drawRoute(lat, lng);
                            if (petugasMarker) setTargetPosition(lat, lng);
                        }
                    } else {
                        info.innerText = `${name} (Sinyal lemah)`;
                    }
                } catch (e) {}
            }

            refreshPetugas();
            setInterval(refreshPetugas, 2000); // Polling 2 detik
        @endif
    </script>
@endpush
