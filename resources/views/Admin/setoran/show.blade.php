@extends('layouts.admin')

@section('title', 'Detail Setoran - ' . ($setoran->user->name ?? 'N/A'))

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

    <style>
        /* ===== DETAIL PAGE STYLES ===== */
        .detail-page {
            min-height: calc(100vh - 100px);
        }

        /* Page Header */
        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--line);
            flex-wrap: wrap;
            gap: 16px;
        }

        .detail-header .page-title {
            font-size: 28px;
            margin-bottom: 4px;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .detail-header .page-subtitle {
            font-size: 15px;
            color: var(--muted);
            line-height: 1.6;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            border: 2px solid transparent;
            background: var(--bg);
        }

        .status-menunggu,
        .status-pending {
            background: #fef3c7;
            color: #92400e;
            border-color: #fbbf24;
        }

        .status-diproses {
            background: #dbeafe;
            color: #1e40af;
            border-color: #60a5fa;
        }

        .status-selesai {
            background: var(--primary-light);
            color: var(--primary-dark);
            border-color: var(--primary);
        }

        .status-dibatalkan,
        .status-ditolak {
            background: #fee2e2;
            color: #991b1b;
            border-color: #f87171;
        }

        /* Main Content Grid */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        @media (max-width: 992px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Information Card */
        .info-card,
        .map-container,
        .items-card {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow-sm);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: var(--primary);
            font-size: 20px;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .info-item {
            padding: 12px 0;
            border-bottom: 1px solid var(--line);
        }

        .info-label {
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-label i {
            width: 16px;
            text-align: center;
            color: var(--primary);
        }

        .info-value {
            font-size: 15px;
            color: var(--ink);
            font-weight: 600;
        }

        .info-value.big {
            font-size: 20px;
            color: var(--primary-dark);
        }

        /* Map */
        .map-container {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        #map {
            flex: 1;
            border-radius: var(--radius-sm);
            border: 1px solid var(--line);
            overflow: hidden;
            min-height: 400px;
        }

        /* KPIs Grid */
        .kpis-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .kpi-item {
            background: var(--bg);
            border: 1px solid var(--line);
            border-radius: var(--radius-sm);
            padding: 16px;
            text-align: center;
        }

        .kpi-label {
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
            margin-bottom: 8px;
        }

        .kpi-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--ink);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .kpi-unit {
            font-size: 12px;
            color: var(--muted);
            margin-left: 4px;
        }

        /* Petugas Info */
        .petugas-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: var(--bg);
            border-radius: var(--radius-sm);
            margin-top: 16px;
            border: 1px solid var(--line);
        }

        .petugas-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary-light);
            display: grid;
            place-items: center;
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 18px;
            flex: 0 0 auto;
        }

        .petugas-details {
            flex: 1;
        }

        .petugas-name {
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 4px;
        }

        .petugas-status {
            font-size: 13px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-online {
            background: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .status-offline {
            background: var(--muted);
        }

        /* Items Table */
        .items-card {
            margin-bottom: 32px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: var(--radius-sm);
            overflow: hidden;
        }

        .items-table thead tr {
            background: var(--primary-light);
        }

        .items-table th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 14px;
            border-bottom: 2px solid var(--primary);
        }

        .items-table tbody tr {
            border-bottom: 1px solid var(--line);
        }

        .items-table tbody tr:hover {
            background: var(--hover-bg);
        }

        .items-table td {
            padding: 16px;
            color: var(--ink);
            font-size: 14px;
        }

        .items-table .total-row {
            background: var(--bg);
            font-weight: 600;
            color: var(--ink);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--line);
        }

        .btn-back,
        .btn-map,
        .btn-process {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-back {
            background: var(--white);
            color: var(--ink);
            border: 1px solid var(--line);
        }

        .btn-back:hover {
            background: var(--hover-bg);
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-map {
            background: var(--primary);
            color: var(--white);
            border: 1px solid var(--primary);
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        }

        .btn-map:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-process {
            background: var(--primary-light);
            color: var(--primary-dark);
            border: 1px solid var(--primary);
        }

        .btn-process:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-1px);
        }

        .loading-state {
            padding: 60px 20px;
            text-align: center;
            background: var(--bg);
            border-radius: var(--radius);
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--line);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@section('content')
    <div class="detail-page">
        <div class="detail-header">
            <div>
                <h1 class="page-title">Detail Setoran</h1>
                <p class="page-subtitle">Informasi lengkap setoran sampah dari pengguna.</p>
            </div>

            <div class="status-badge status-{{ $setoran->status }}">
                @if ($setoran->status == 'pending' || $setoran->status == 'menunggu')
                    <i class="fa-solid fa-clock"></i> <span>MENUNGGU</span>
                @elseif($setoran->status == 'diproses')
                    <i class="fa-solid fa-spinner"></i> <span>DIPROSES</span>
                @elseif($setoran->status == 'selesai')
                    <i class="fa-solid fa-check-circle"></i> <span>SELESAI</span>
                @else
                    <i class="fa-solid fa-times-circle"></i> <span>DIBATALKAN</span>
                @endif
            </div>
        </div>

        <div class="detail-grid">
            <div class="info-card">
                <div class="card-title">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Informasi Setoran</span>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label"><i class="fa-solid fa-user"></i> <span>Pengguna</span></div>
                        <div class="info-value">{{ $setoran->user->name ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label"><i class="fa-solid fa-phone"></i> <span>Telepon</span></div>
                        <div class="info-value">{{ $setoran->user->phone ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label"><i class="fa-solid fa-truck"></i> <span>Metode</span></div>
                        <div class="info-value">
                            @if ($setoran->metode == 'jemput')
                                <span style="color: var(--primary-dark); font-weight: 600;"><i
                                        class="fa-solid fa-truck-pickup"></i> Penjemputan</span>
                            @else
                                <span style="color: var(--primary-dark); font-weight: 600;"><i class="fa-solid fa-box"></i>
                                    Antar Sendiri</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label"><i class="fa-solid fa-calendar"></i> <span>Tanggal</span></div>
                        <div class="info-value">{{ $setoran->created_at->format('d M Y, H:i') }}</div>
                    </div>

                    @if ($setoran->metode === 'jemput' && $setoran->alamat)
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label"><i class="fa-solid fa-location-dot"></i> <span>Alamat Penjemputan</span>
                            </div>
                            <div class="info-value">{{ $setoran->alamat ?? '-' }}</div>
                        </div>
                    @endif

                    <div class="info-item" style="grid-column: 1 / -1;">
                        <div class="info-label"><i class="fa-solid fa-file-alt"></i> <span>Catatan</span></div>
                        <div class="info-value">{{ $setoran->catatan ?? 'Tidak ada catatan' }}</div>
                    </div>
                </div>

                @if ($setoran->petugas)
                    <div class="petugas-info">
                        <div class="petugas-avatar">{{ substr($setoran->petugas->name, 0, 1) }}</div>
                        <div class="petugas-details">
                            <div class="petugas-name">{{ $setoran->petugas->name }}</div>
                            <div class="petugas-status">
                                <span class="status-dot status-online"></span>
                                <span>Petugas Penjemput</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if ($setoran->metode === 'jemput' && $setoran->latitude && $setoran->longitude)
                <div class="map-container">
                    <div class="card-title">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Lokasi Penjemputan</span>
                    </div>

                    <div id="map"></div>

                    <div class="kpis-grid">
                        <div class="kpi-item">
                            <div class="kpi-label">Estimasi Waktu</div>
                            <div class="kpi-value"><span id="etaText">-</span><span class="kpi-unit"
                                    id="etaUnit">-</span></div>
                        </div>
                        <div class="kpi-item">
                            <div class="kpi-label">Jarak Tempuh</div>
                            <div class="kpi-value"><span id="distText">-</span><span class="kpi-unit"
                                    id="distUnit">-</span></div>
                        </div>
                        <div class="kpi-item">
                            <div class="kpi-label">Terakhir Dilihat</div>
                            <div class="kpi-value" style="font-size: 18px;"><span id="seenText">-</span></div>
                        </div>
                    </div>

                    <div id="petugasInfo"
                        style="margin-top: 16px; padding: 12px; background: var(--bg); border-radius: var(--radius-sm); font-size: 14px; color: var(--muted);">
                        <i class="fa-solid fa-spinner fa-spin"></i> Memuat lokasi petugas...
                    </div>
                </div>
            @else
                <div class="map-container">
                    <div class="card-title">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Informasi Lokasi</span>
                    </div>
                    <div class="loading-state">
                        <i class="fa-solid fa-store" style="font-size: 3rem; color: var(--line); margin-bottom: 15px;"></i>
                        <div style="font-size: 16px; color: var(--muted); margin-bottom: 8px;">Metode Antar Sendiri</div>
                        <div style="font-size: 14px; color: var(--muted);">Tidak ada data lokasi penjemputan untuk metode
                            ini.</div>
                    </div>
                </div>
            @endif
        </div>

        @if (isset($setoran->items) && count($setoran->items) > 0)
            <div class="items-card">
                <div class="card-title">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Detail Sampah</span>
                </div>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Jenis Sampah</th>
                            <th>Kategori Induk</th>
                            <th>Berat/Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($setoran->items as $item)
                            <tr>
                                {{-- FIX: Menggunakan relasi kategori untuk nama --}}
                                <td style="font-weight: 700;">{{ $item->kategori->nama_sampah ?? 'N/A' }}</td>

                                {{-- FIX: Menambahkan kolom kategori induk --}}
                                <td>
                                    <span
                                        style="background: #f1f5f9; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; border:1px solid #e2e8f0;">
                                        {{ $item->kategori->masterKategori->nama_kategori ?? '-' }}
                                    </span>
                                </td>

                                {{-- FIX: Menggunakan kolom 'jumlah' dan 'satuan' --}}
                                <td>{{ number_format($item->jumlah, 2) }} {{ $item->satuan ?? 'kg' }}</td>

                                {{-- FIX: Menggunakan kolom 'harga_satuan' --}}
                                <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>

                                <td style="font-weight: 800; color: var(--primary-dark);">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach

                        <tr class="total-row">
                            <td colspan="4" style="text-align: right; font-weight: 600;">Total Estimasi:</td>
                            <td style="font-size: 18px; color: var(--primary-dark); font-weight: 800;">
                                Rp {{ number_format($setoran->estimasi_total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        <div class="action-buttons">
            <a href="{{ route('admin.setoran.index') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Daftar</span>
            </a>

            <a href="{{ route('admin.map') }}" class="btn-map">
                <i class="fa-solid fa-map"></i>
                <span>Lihat Peta Setoran</span>
            </a>

            @if ($setoran->status == 'menunggu' || $setoran->status == 'pending')
                <a href="{{ route('admin.setoran.edit', $setoran->id) }}" class="btn-process">
                    <i class="fa-solid fa-play"></i>
                    <span>Proses Setoran</span>
                </a>
            @endif

            @if ($setoran->status == 'diproses')
                <a href="{{ route('admin.setoran.complete', $setoran->id) }}" class="btn-process">
                    <i class="fa-solid fa-check"></i>
                    <span>Tandai Selesai</span>
                </a>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <script>
        @if ($setoran->metode === 'jemput' && $setoran->latitude && $setoran->longitude)
            const tujuanLat = Number("{{ $setoran->latitude }}");
            const tujuanLng = Number("{{ $setoran->longitude }}");
            const urlLoc = "{{ route('admin.setoran.petugas_location', $setoran->id) }}";

            // Initialize map
            const map = L.map('map').setView([tujuanLat, tujuanLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Pickup location marker
            const pickupIcon = L.divIcon({
                html: '<div style="background: var(--primary); width: 40px; height: 40px; border-radius: 50%; display: grid; place-items: center; color: white; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><i class="fa-solid fa-location-dot"></i></div>',
                className: '',
                iconSize: [40, 40],
                iconAnchor: [20, 40]
            });

            L.marker([tujuanLat, tujuanLng], {
                    icon: pickupIcon
                })
                .addTo(map)
                .bindPopup("<b>Titik Penjemputan</b><br>" + ("{{ $setoran->alamat }}" || "Lokasi penjemputan"))
                .openPopup();

            // Truck icon
            const truckIcon = L.divIcon({
                html: '<div style="background: #3b82f6; width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; color: white; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); transform: rotate(45deg);"><i class="fa-solid fa-truck"></i></div>',
                className: '',
                iconSize: [36, 36],
                iconAnchor: [18, 18]
            });

            let petugasMarker = null;
            let routeLine = null;

            // Utility functions
            function formatDistance(meters) {
                if (meters < 1000) {
                    return {
                        value: meters.toFixed(0),
                        unit: 'm'
                    };
                } else {
                    return {
                        value: (meters / 1000).toFixed(1),
                        unit: 'km'
                    };
                }
            }

            function formatDuration(seconds) {
                const minutes = Math.round(seconds / 60);
                if (minutes < 60) {
                    return {
                        value: minutes,
                        unit: 'menit'
                    };
                } else {
                    const hours = Math.floor(minutes / 60);
                    const remainingMinutes = minutes % 60;
                    return {
                        value: hours,
                        unit: 'jam',
                        extra: remainingMinutes
                    };
                }
            }

            // Route drawing
            async function drawRoute(fromLat, fromLng) {
                const url =
                    `https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${tujuanLng},${tujuanLat}?overview=full&geometries=geojson`;

                try {
                    const res = await fetch(url, {
                        cache: "no-store"
                    });
                    const data = await res.json();

                    if (!data.routes || !data.routes[0]) return;

                    const route = data.routes[0];
                    const coords = route.geometry.coordinates.map(c => [c[1], c[0]]);

                    // Update route line
                    if (routeLine) {
                        routeLine.setLatLngs(coords);
                    } else {
                        routeLine = L.polyline(coords, {
                            color: 'var(--primary)',
                            weight: 4,
                            opacity: 0.7,
                            dashArray: '10, 10'
                        }).addTo(map);
                    }

                    // Update ETA and distance
                    const duration = formatDuration(route.duration);
                    const distance = formatDistance(route.distance);

                    document.getElementById('etaText').textContent = duration.value;
                    document.getElementById('etaUnit').textContent = duration.unit;
                    if (duration.extra) {
                        document.getElementById('etaUnit').textContent = `${duration.unit} ${duration.extra} menit`;
                    }

                    document.getElementById('distText').textContent = distance.value;
                    document.getElementById('distUnit').textContent = distance.unit;

                    return route;
                } catch (error) {
                    console.error('Error fetching route:', error);
                }
            }

            // Update petugas position
            function updatePetugasPosition(lat, lng, lastSeen) {
                if (!petugasMarker) {
                    petugasMarker = L.marker([lat, lng], {
                            icon: truckIcon
                        })
                        .addTo(map)
                        .bindPopup("<b>Truk Petugas</b><br>Sedang menuju lokasi penjemputan");
                } else {
                    petugasMarker.setLatLng([lat, lng]);
                }

                // Fit bounds to show both markers
                const bounds = L.latLngBounds([
                    [lat, lng],
                    [tujuanLat, tujuanLng]
                ]);
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });

                // Update last seen time
                if (lastSeen) {
                    document.getElementById('seenText').textContent = lastSeen;
                }
            }

            // Refresh petugas location
            async function refreshPetugasLocation() {
                try {
                    const res = await fetch(urlLoc, {
                        cache: "no-store"
                    });
                    const data = await res.json();

                    const infoElement = document.getElementById('petugasInfo');

                    if (!data.petugas_id) {
                        infoElement.innerHTML = '<i class="fa-solid fa-clock"></i> Menunggu petugas mengambil order...';
                        return;
                    }

                    if (data.petugas_latitude && data.petugas_longitude) {
                        const lat = Number(data.petugas_latitude);
                        const lng = Number(data.petugas_longitude);

                        updatePetugasPosition(lat, lng, data.petugas_last_seen);
                        await drawRoute(lat, lng);

                        infoElement.innerHTML =
                            `<i class="fa-solid fa-truck-moving"></i> Petugas <b>${data.petugas_name}</b> sedang dalam perjalanan...`;
                    } else {
                        infoElement.innerHTML =
                            `<i class="fa-solid fa-signal"></i> Petugas <b>${data.petugas_name}</b> sedang mengambil order...`;
                    }

                } catch (error) {
                    console.error('Error fetching petugas location:', error);
                    document.getElementById('petugasInfo').innerHTML =
                        '<i class="fa-solid fa-exclamation-triangle"></i> Gagal memuat lokasi petugas';
                }
            }

            // Initial load
            refreshPetugasLocation();

            // Refresh every 3 seconds
            setInterval(refreshPetugasLocation, 3000);

            // Auto-refresh when tab becomes visible
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    refreshPetugasLocation();
                }
            });
        @endif
    </script>
@endpush
