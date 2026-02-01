@extends('layouts.user')
@section('title', 'Buat Setoran')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

    <style>
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
            --radius-sm: 12px;
        }

        body,
        .page,
        .card,
        input,
        select,
        textarea,
        button,
        a {
            font-family: "Plus Jakarta Sans", sans-serif;
            background-color: var(--bg);
        }

        .container-fluid {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 0 16px;
        }

        @media (min-width:768px) {
            .container-fluid {
                padding: 0 24px;
            }
        }

        /* ===== MODERN HERO HEADER ===== */
        .page-header {
            /* Gradient yang lebih dalam & rich */
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);

            /* Tambahkan tekstur dot halus agar tidak flat */
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px),
                linear-gradient(135deg, #10b981 0%, #047857 100%);
            background-size: 24px 24px, 100% 100%;

            /* Padding diperbesar: Atas 40px, Bawah 90px (untuk ruang overlap) */
            padding: 40px 0 90px;

            color: #fff;
            position: relative;

            /* Lengkungan lebih ekstrem dan smooth */
            border-radius: 0 0 50px 50px;

            /* Shadow lembut untuk depth */
            box-shadow: 0 10px 30px -10px rgba(16, 185, 129, 0.5);

            /* Overlap: Kartu di bawahnya akan naik 60px menutupi header ini */
            margin-bottom: -60px;
            z-index: 1;
            overflow: hidden;
        }

        /* Dekorasi Circle (Glassmorphism) - Diperbesar & Dipertajam */
        .page-header::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            top: -100px;
            left: -50px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .page-header::after {
            content: "";
            position: absolute;
            width: 250px;
            height: 250px;
            bottom: -50px;
            right: -20px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .header-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
        }

        .title {
            margin: 0;
            font-size: 1.75rem;
            /* Font lebih besar sedikit */
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .subtitle {
            margin-top: 10px;
            font-size: 1rem;
            opacity: 0.95;
            line-height: 1.6;
            font-weight: 500;
            color: #ecfdf5;
            /* Warna putih kehijauan agar soft */
        }

        /* ===== CARD & LAYOUT ===== */
        .page {
            padding-bottom: 100px;
        }

        /* Space untuk fixed footer */

        .main-card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.5);
            position: relative;
            z-index: 10;
            overflow: hidden;
            margin-top: 10px;
        }

        .card-head {
            padding: 18px 20px;
            border-bottom: 1px solid var(--line);
            background: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: var(--brand-dark);
        }

        .content {
            padding: 20px;
        }

        /* ===== FORMS ===== */
        label {
            font-weight: 800;
            color: var(--ink);
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--line);
            font-weight: 600;
            color: var(--ink);
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
            outline: none;
        }

        .grid2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 680px) {
            .grid2 {
                grid-template-columns: 1fr;
            }
        }

        .row {
            margin-bottom: 20px;
        }

        .hint {
            margin-top: 6px;
            color: var(--muted);
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ===== MAP UI ===== */
        .map-wrapper {
            border: 2px solid var(--line);
            border-radius: var(--radius);
            overflow: hidden;
            background: #fff;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
        }

        .map-container {
            position: relative;
            height: 320px;
        }

        #map {
            height: 100%;
            width: 100%;
            z-index: 1;
        }

        .map-overlay-tools {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 1000;
        }

        .btn-tool {
            background: var(--brand);
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 0.8rem;
            transition: 0.2s;
        }

        .btn-tool:hover {
            transform: scale(1.05);
            background: #047857;
        }

        .coordinate-display {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1px;
            background: var(--line);
            border-top: 1px solid var(--line);
        }

        .coord-item {
            background: #f8fafc;
            padding: 10px;
            font-size: 0.7rem;
            color: var(--muted);
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .coord-item span {
            color: var(--ink);
            font-family: monospace;
            font-size: 0.8rem;
        }

        /* ===== TABLE & BUTTONS ===== */
        .btnx {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            transition: .2s;
            text-decoration: none;
        }

        .btnx:hover {
            background: #f1f5f9;
        }

        .btnx-primary {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .btnx-primary:hover {
            background: var(--brand-dark);
            transform: translateY(-1px);
        }

        .btnx-danger {
            border-color: #fee2e2;
            color: #ef4444;
            background: #fef2f2;
            padding: 8px 12px;
        }

        .add-sticky {
            position: sticky;
            top: 10px;
            z-index: 20;
            display: flex;
            justify-content: flex-end;
            margin: 0 0 10px;
        }

        .btn-add {
            padding: 10px 16px;
            border-radius: 50px;
            border: none;
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: #fff;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            font-size: 0.8rem;
            font-weight: 800;
        }

        .table-wrap {
            overflow: auto;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: #fff;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 820px;
        }

        thead th {
            text-align: left;
            font-size: 0.75rem;
            color: var(--muted);
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            background: #f8fafc;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .total-box {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-radius: var(--radius);
            background: var(--brand-soft);
            font-weight: 800;
            color: #065f46;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        /* ===== LEGACY ACTION BAR (FIXED BOTTOM) ===== */
        .actionbar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 16px 0;
            background: rgba(255, 255, 255, 0.95);
            border-top: 1px solid var(--line);
            backdrop-filter: blur(8px);
            z-index: 999;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.03);
        }

        .actionbar .inner {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
        }
    </style>
@endpush

@section('content')
    <div class="page">
        {{-- MODERN HEADER --}}
        <div class="page-header">
            <div class="container-fluid">
                <div class="header-content">
                    <h2 class="title"><i class="fa-solid fa-leaf"></i> Setor Sampah</h2>
                    <p class="subtitle">
                        Ubah sampahmu menjadi rupiah. Pilih metode, tentukan lokasi, dan biarkan kami yang mengurus sisanya.
                    </p>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <form method="POST" action="{{ route('user.setoran.store') }}" id="formSetoran">
                @csrf

                {{-- MAIN CARD --}}
                <div class="card main-card">
                    <div class="card-head">
                        <i class="fa-regular fa-clipboard" style="color:var(--brand)"></i> Detail Permintaan
                    </div>

                    <div class="content">
                        {{-- METODE & JADWAL --}}
                        <div class="row grid2">
                            <div>
                                <label><i class="fa-solid fa-truck-fast"></i> Metode Penyerahan *</label>
                                <select name="metode" id="metodeSelect" required>
                                    <option value="antar" {{ old('metode', 'antar') == 'antar' ? 'selected' : '' }}>Saya
                                        Antar Sendiri</option>
                                    <option value="jemput" {{ old('metode') == 'jemput' ? 'selected' : '' }}>Petugas Jemput
                                        ke Lokasi</option>
                                </select>
                            </div>
                            <div>
                                <label><i class="fa-regular fa-calendar-check"></i> Jadwal *</label>
                                <div class="grid2" style="gap: 10px;">
                                    <input type="date" id="ui_tgl_jemput" min="{{ date('Y-m-d') }}"
                                        value="{{ date('Y-m-d') }}">
                                    <select id="ui_slot_waktu">
                                        <option value="08:00">Pagi (08:00 - 10:00)</option>
                                        <option value="10:00">Siang (10:00 - 12:00)</option>
                                        <option value="13:00">Sore (13:00 - 15:00)</option>
                                    </select>
                                </div>
                                <input type="hidden" name="jadwal_jemput" id="final_jadwal_jemput">
                            </div>
                        </div>

                        {{-- JEMPUT SECTION (MAP) --}}
                        <div id="jemputFields" style="display:none;">
                            <hr style="border:0; border-top:1px dashed var(--line); margin: 24px 0;">

                            <div class="row">
                                <label><i class="fa-solid fa-map-location-dot"></i> Titik Jemput (Wajib)</label>
                                <div class="map-wrapper">
                                    <div class="map-container">
                                        <div id="map"></div>
                                        <div class="map-overlay-tools">
                                            <button type="button" class="btn-tool" id="btnGps">
                                                <i class="fa-solid fa-location-crosshairs"></i> <span>Gunakan Lokasi
                                                    Saya</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="coordinate-display">
                                        <div class="coord-item">LAT: <span id="latView">-</span></div>
                                        <div class="coord-item">LNG: <span id="lngView">-</span></div>
                                    </div>
                                </div>
                                <div id="locStatus" class="hint">
                                    <i class="fa-solid fa-circle-info"></i> Aktifkan GPS, lalu klik tombol di atas peta.
                                </div>
                            </div>

                            <div class="row">
                                <label>Alamat Lengkap / Patokan *</label>
                                <textarea name="alamat" id="alamatInput" rows="2" placeholder="Contoh: Pagar hitam, samping warung bakso..."
                                    required>{{ old('alamat') }}</textarea>
                            </div>
                            <input type="hidden" name="latitude" id="latInput">
                            <input type="hidden" name="longitude" id="lngInput">
                        </div>

                        <hr style="border:0; border-top:1px dashed var(--line); margin: 24px 0;">

                        {{-- ITEMS TABLE --}}
                        <div class="add-sticky">
                            <button class="btn-add" id="btnAddJenis" type="button" onclick="addRow()">
                                <i class="fa-solid fa-plus"></i> Tambah Item
                            </button>
                        </div>

                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width:35%">Jenis Sampah</th>
                                        <th style="width:15%">Berat</th>
                                        <th style="width:10%">Satuan</th>
                                        <th style="width:20%">Estimasi Harga</th>
                                        <th style="width:20%">Subtotal</th>
                                        <th style="width:1%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody"></tbody>
                            </table>
                        </div>

                        <div class="total-box">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <i class="fa-solid fa-wallet"></i> Total Estimasi
                            </div>
                            <div style="font-size:1.1rem;">Rp <span id="grandTotal">0</span></div>
                        </div>

                        <div class="row" style="margin-top:20px;">
                            <label>Catatan Tambahan (Opsional)</label>
                            <textarea name="catatan" rows="2" placeholder="Pesan untuk petugas...">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ACTION BAR (LEGACY STYLE) --}}
                <div class="actionbar">
                    <div class="container-fluid">
                        <div class="inner">
                            <a class="btnx" href="{{ route('user.dashboard') }}">
                                <i class="fa-solid fa-arrow-left"></i> Kembali
                            </a>
                            <button class="btnx btnx-primary" type="submit">
                                <i class="fa-solid fa-paper-plane"></i> Kirim Setoran
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        const kategoriData = @json($kategoriData);
        let rowIndex = 0;
        const rupiah = (n) => new Intl.NumberFormat('id-ID').format(n);

        // --- ITEM LOGIC ---
        function buildSelect(name) {
            let html = `<select name="${name}" onchange="recalc()" required><option value="">-- Pilih Sampah --</option>`;
            kategoriData.forEach(k => {
                html +=
                    `<option value="${k.id}" data-harga="${k.harga}" data-satuan="${k.satuan}">${k.nama}</option>`;
            });
            return html + `</select>`;
        }

        function addRow() {
            const tbody = document.getElementById('itemsBody');
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td>${buildSelect(`items[${rowIndex}][kategori_sampah_id]`)}</td>
            <td><input type="number" name="items[${rowIndex}][jumlah]" step="0.01" min="0.01" value="1" oninput="recalc()" required placeholder="0"></td>
            <td class="satuanCell text-muted" style="text-align:center">-</td>
            <td class="hargaCell text-muted">-</td>
            <td class="subtotalCell" style="color:var(--brand-dark)">0</td>
            <td style="text-align:center"><button type="button" class="btnx btnx-danger" onclick="this.closest('tr').remove(); recalc();"><i class="fa-solid fa-trash-can"></i></button></td>
        `;
            tbody.appendChild(tr);
            rowIndex++;
            recalc();
        }

        function recalc() {
            let grand = 0;
            document.querySelectorAll('#itemsBody tr').forEach(tr => {
                const select = tr.querySelector('select');
                const qtyInput = tr.querySelector('input[type="number"]');
                const opt = select.options[select.selectedIndex];

                const harga = parseInt(opt?.dataset?.harga || 0);
                const qty = parseFloat(qtyInput.value || 0);
                const sub = Math.round(qty * harga);

                tr.querySelector('.satuanCell').innerText = opt?.dataset?.satuan || '-';
                tr.querySelector('.hargaCell').innerText = harga ? 'Rp ' + rupiah(harga) : '-';
                tr.querySelector('.subtotalCell').innerText = rupiah(sub);
                grand += sub;
            });
            document.getElementById('grandTotal').innerText = rupiah(grand);
        }

        // --- MAP & GPS LOGIC (Auto Fill) ---
        let map, marker;
        const latInput = document.getElementById('latInput'),
            lngInput = document.getElementById('lngInput');
        const latView = document.getElementById('latView'),
            lngView = document.getElementById('lngView');
        const locStatus = document.getElementById('locStatus'),
            alamatInput = document.getElementById('alamatInput');

        function initMap() {
            // Default View: Monas Jakarta (bisa diubah ke Riau sesuai kebutuhan projectmu)
            map = L.map('map', {
                scrollWheelZoom: false
            }).setView([0.5071, 101.4478], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OSM'
            }).addTo(map);

            // Manual Click
            map.on('click', e => setCoordinate(e.latlng.lat, e.latlng.lng, 'Titik dipilih manual.', false));
        }

        function setCoordinate(lat, lng, msg, forceFill) {
            latInput.value = lat;
            lngInput.value = lng;
            latView.innerText = lat.toFixed(6);
            lngView.innerText = lng.toFixed(6);

            if (!marker) {
                marker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(map);
                // Drag marker tidak auto-fill alamat (takut menimpa editan user), kecuali user minta
                marker.on('dragend', e => setCoordinate(e.target.getLatLng().lat, e.target.getLatLng().lng,
                    'Lokasi digeser.', false));
            } else {
                marker.setLatLng([lat, lng]);
            }

            map.setView([lat, lng], 17);
            locStatus.innerHTML =
                `<span style="color:var(--brand-dark)"><i class="fa-solid fa-check-circle"></i> ${msg}</span>`;

            reverseGeocode(lat, lng, forceFill);
        }

        async function reverseGeocode(lat, lng, forceFill) {
            if (forceFill) locStatus.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Mengambil alamat...';

            try {
                const res = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=id`
                );
                const data = await res.json();

                // Logic: Auto fill hanya jika tombol GPS ditekan (forceFill) atau field alamat masih kosong/sedikit
                if (data.display_name) {
                    if (forceFill || alamatInput.value.length < 5) {
                        alamatInput.value = data.display_name;
                        locStatus.innerHTML =
                            '<span style="color:var(--brand-dark)"><i class="fa-solid fa-map-pin"></i> Alamat otomatis terisi!</span>';
                    }
                }
            } catch (e) {
                console.error("Geocoding error");
            }
        }

        // --- EVENTS ---
        document.getElementById('btnGps').addEventListener('click', () => {
            if (!navigator.geolocation) {
                alert('Browser tidak mendukung GPS.');
                return;
            }
            locStatus.innerHTML = '<i class="fa-solid fa-satellite-dish"></i> Mencari sinyal GPS...';

            navigator.geolocation.getCurrentPosition(
                p => setCoordinate(p.coords.latitude, p.coords.longitude, 'GPS Berhasil!',
                    true), // True = Paksa isi alamat
                e => locStatus.innerHTML =
                '<span class="text-danger">Gagal akses GPS. Pastikan Location aktif.</span>', {
                    enableHighAccuracy: true
                }
            );
        });

        document.getElementById('metodeSelect').addEventListener('change', function() {
            const isJemput = this.value === 'jemput';
            document.getElementById('jemputFields').style.display = isJemput ? 'block' : 'none';
            if (isJemput) setTimeout(() => map.invalidateSize(), 300);
        });

        document.getElementById('formSetoran').addEventListener('submit', function(e) {
            // Merge Date + Time
            const tgl = document.getElementById('ui_tgl_jemput').value;
            const jam = document.getElementById('ui_slot_waktu').value;
            document.getElementById('final_jadwal_jemput').value = tgl + 'T' + jam;

            // Validasi Maps
            if (document.getElementById('metodeSelect').value === 'jemput' && !latInput.value) {
                e.preventDefault();
                // Scroll ke map agar user sadar
                document.getElementById('jemputFields').scrollIntoView({
                    behavior: 'smooth'
                });
                alert("Mohon pilih titik penjemputan pada peta!");
            }
        });

        window.onload = () => {
            initMap();
            addRow();
        };
    </script>
@endpush
