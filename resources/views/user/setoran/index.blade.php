@extends('layouts.user')
@section('title', 'Riwayat Setoran')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" />

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
        table,
        a,
        button {
            font-family: "Plus Jakarta Sans", sans-serif;
            background-color: var(--bg);
        }

        .container-fluid {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px;
        }

        @media (min-width:768px) {
            .container-fluid {
                padding: 0 32px;
            }
        }

        /* ===== HERO HEADER (MATCHING STYLE) ===== */
        .page-header {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            padding: 40px 0 80px;
            color: #fff;
            position: relative;
            border-radius: 0 0 50px 50px;
            box-shadow: 0 10px 30px -10px rgba(16, 185, 129, 0.5);
            margin-bottom: -50px;
            z-index: 1;
            overflow: hidden;
        }

        .header-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .title {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .subtitle {
            margin-top: 8px;
            opacity: 0.9;
            font-size: 1rem;
            font-weight: 500;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Dekorasi Header */
        .page-header::before,
        .page-header::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            pointer-events: none;
        }

        .page-header::before {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -50px;
        }

        .page-header::after {
            width: 200px;
            height: 200px;
            bottom: -50px;
            right: -20px;
            opacity: 0.6;
        }

        /* ===== MAIN CARD ===== */
        .card-wrap {
            position: relative;
            z-index: 10;
            margin-top: 10px;
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.6);
            overflow: hidden;
        }

        /* ===== TOOLBAR ===== */
        .toolbar {
            padding: 20px;
            border-bottom: 1px solid var(--line);
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .toolbar-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--ink);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btnx {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            transition: 0.2s;
            cursor: pointer;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
        }

        .btnx:hover {
            background: #f8fafc;
            transform: translateY(-1px);
        }

        .btnx-primary {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .btnx-primary:hover {
            background: var(--brand-dark);
            transform: translateY(-2px);
        }

        /* ===== TABLE ===== */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        thead th {
            text-align: left;
            padding: 16px 20px;
            background: #f8fafc;
            color: var(--muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--line);
        }

        tbody tr {
            transition: 0.15s;
            border-bottom: 1px solid var(--line);
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background-color: #f1f5f9;
        }

        tbody td {
            padding: 16px 20px;
            vertical-align: middle;
            color: var(--ink);
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* Custom Column Styles */
        .td-id {
            width: 60px;
            text-align: center;
            color: var(--muted);
        }

        .td-items ul {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 0.85rem;
        }

        .td-items li {
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .td-items .qty {
            background: var(--line);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 800;
        }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-pending {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }

        .badge-process {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }

        .badge-success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #d1fae5;
        }

        .badge-cancel {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fee2e2;
        }

        .price {
            font-family: monospace;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--brand-dark);
        }

        /* Map Links */
        .map-links {
            display: flex;
            gap: 8px;
        }

        .btn-mini {
            padding: 6px 10px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid var(--line);
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--muted);
            text-decoration: none;
        }

        .btn-mini:hover {
            color: var(--brand);
            border-color: var(--brand);
        }

        /* ===== FOOTER PAGINATION ===== */
        .card-footer {
            padding: 16px 20px;
            background: #fff;
            border-top: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .pagination {
            display: flex;
            gap: 6px;
        }

        .page-link {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .page-item.active .page-link {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-icon {
            font-size: 3rem;
            color: var(--line);
            margin-bottom: 15px;
        }

        .empty-text {
            color: var(--muted);
            font-weight: 600;
            margin-bottom: 20px;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrap">

        {{-- MODERN HEADER --}}
        <div class="page-header">
            <div class="container-fluid">
                <div class="header-content">
                    <h2 class="title">Riwayat Setoran</h2>
                    <p class="subtitle">Pantau status penjemputan dan estimasi pendapatan dari sampah yang kamu setor.</p>
                </div>
            </div>
        </div>

        <div class="container-fluid">

            {{-- SUCCESS ALERT --}}
            @if (session('success'))
                <div
                    style="background: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 15px; border-radius: 12px; margin-bottom: 20px; display:flex; align-items:center; gap:10px; font-weight:700; box-shadow: 0 4px 12px rgba(16,185,129,0.1);">
                    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            {{-- TABLE CARD --}}
            <div class="card-wrap">
                <div class="toolbar">
                    <div class="toolbar-title">
                        <i class="fa-solid fa-clock-rotate-left" style="color:var(--muted)"></i> Data Transaksi
                    </div>
                    <div style="display:flex; gap:10px;">
                        <a href="{{ route('user.dashboard') }}" class="btnx">
                            <i class="fa-solid fa-house"></i> Dashboard
                        </a>
                        <a href="{{ route('user.setoran.create') }}" class="btnx btnx-primary">
                            <i class="fa-solid fa-plus"></i> Setor Sampah
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th class="td-id">#</th>
                                <th>Tanggal & Metode</th>
                                <th style="width:30%">Item Sampah</th>
                                <th>Total Estimasi</th>
                                <th>Status</th>
                                <th>Lokasi Penjemputan</th>
                                <th style="text-align:right">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $it)
                                <tr>
                                    <td class="td-id">
                                        {{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}
                                    </td>

                                    <td>
                                        <div style="font-weight:800; color:var(--ink)">
                                            {{ \Carbon\Carbon::parse($it->created_at)->format('d M Y') }}
                                        </div>
                                        <div style="font-size:0.8rem; color:var(--muted); margin-top:4px;">
                                            @if ($it->metode == 'jemput')
                                                <i class="fa-solid fa-truck-fast"></i> Jemput
                                            @else
                                                <i class="fa-solid fa-person-walking-luggage"></i> Antar
                                            @endif
                                        </div>
                                    </td>

                                    <td class="td-items">
                                        @if ($it->items->count() > 0)
                                            <ul>
                                                @foreach ($it->items->take(3) as $d)
                                                    <li>
                                                        <span class="qty">{{ $d->jumlah }}
                                                            {{ $d->satuan ?? 'kg' }}</span>
                                                        {{ $d->kategori->nama_sampah ?? 'Item dihapus' }}
                                                    </li>
                                                @endforeach
                                                @if ($it->items->count() > 3)
                                                    <li style="color:var(--brand); font-size:0.75rem;">
                                                        +{{ $it->items->count() - 3 }} item lainnya...</li>
                                                @endif
                                            </ul>
                                        @else
                                            <span class="muted">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="price">Rp {{ number_format($it->estimasi_total, 0, ',', '.') }}</span>
                                    </td>

                                    <td>
                                        @php
                                            $badges = [
                                                'menunggu' => ['class' => 'badge-pending', 'icon' => 'fa-clock'],
                                                'proses' => [
                                                    'class' => 'badge-process',
                                                    'icon' => 'fa-spinner fa-spin',
                                                ],
                                                'selesai' => ['class' => 'badge-success', 'icon' => 'fa-check-circle'],
                                                'batal' => ['class' => 'badge-cancel', 'icon' => 'fa-times-circle'],
                                                ];
                                            $status = strtolower($it->status);
                                            $b = $badges[$status] ?? [
                                                'class' => 'badge-pending',
                                                'icon' => 'fa-circle-question',
                                            ];
                                        @endphp
                                        <span class="badge {{ $b['class'] }}">
                                            <i class="fa-solid {{ $b['icon'] }}"></i> {{ $it->status }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($it->metode === 'jemput' && $it->latitude)
                                            <div class="map-links">
                                                <a href="https://www.google.com/maps/search/?api=1&query={{ $it->latitude }},{{ $it->longitude }}"
                                                    target="_blank" class="btn-mini" title="Lihat Peta">
                                                    <i class="fa-solid fa-map-location-dot"></i> Peta
                                                </a>
                                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $it->latitude }},{{ $it->longitude }}"
                                                    target="_blank" class="btn-mini" title="Navigasi">
                                                    <i class="fa-solid fa-location-arrow"></i> Rute
                                                </a>
                                            </div>
                                        @else
                                            <span class="muted" style="font-size:0.8rem; font-style:italic;">
                                                @if ($it->metode == 'antar')
                                                    - (Diantar ke gudang)
                                                @else
                                                    Lokasi tidak ada
                                                @endif
                                            </span>
                                        @endif
                                    </td>

                                    <td style="text-align:right">
                                        <a href="{{ route('user.setoran.show', $it->id) }}" class="btnx"
                                            style="padding:6px 12px; font-size:0.8rem;">
                                            Detail <i class="fa-solid fa-chevron-right"
                                                style="font-size:0.7rem; margin-left:4px;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <div class="empty-icon"><i class="fa-regular fa-folder-open"></i></div>
                                            <div class="empty-text">Belum ada riwayat setoran sampah.</div>
                                            <a href="{{ route('user.setoran.create') }}" class="btnx btnx-primary">Mulai
                                                Setor Sekarang</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- FOOTER / PAGINATION --}}
                <div class="card-footer">
                    <div style="font-size:0.85rem; color:var(--muted); font-weight:600;">
                        Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} dari
                        {{ $items->total() }} data
                    </div>
                    <div>
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
