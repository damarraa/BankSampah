@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@push('styles')
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
            --radius: 16px;
        }

        body {
            background-color: var(--bg);
            color: var(--ink);
            font-family: "Plus Jakarta Sans", sans-serif;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding-bottom: 60px;
        }

        /* ===== HEADER ===== */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title h1 {
            font-size: 1.75rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-title p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .date-badge {
            background: #fff;
            padding: 8px 16px;
            border-radius: 50px;
            border: 1px solid var(--line);
            font-weight: 700;
            color: var(--ink);
            font-size: 0.9rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ===== QUICK STATS (TOP ROW) ===== */
        .quick-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--card);
            padding: 20px;
            border-radius: var(--radius);
            border: 1px solid var(--line);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
        }

        /* Decorative Line Top */
        .stat-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--color-accent);
        }

        .st-primary {
            --color-accent: #10b981;
            --bg-icon: #ecfdf5;
            --text-icon: #059669;
        }

        .st-blue {
            --color-accent: #3b82f6;
            --bg-icon: #eff6ff;
            --text-icon: #2563eb;
        }

        .st-orange {
            --color-accent: #f59e0b;
            --bg-icon: #fffbeb;
            --text-icon: #d97706;
        }

        .st-purple {
            --color-accent: #8b5cf6;
            --bg-icon: #f5f3ff;
            --text-icon: #7c3aed;
        }

        .stat-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--bg-icon);
            color: var(--text-icon);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--ink);
            line-height: 1.1;
            margin-bottom: 4px;
        }

        .stat-sub {
            font-size: 0.8rem;
            color: var(--muted);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stat-trend {
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }

        .trend-up {
            color: #10b981;
        }

        .trend-down {
            color: #ef4444;
        }

        /* ===== MAIN GRID ===== */
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Chart Card */
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            height: 100%;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--ink);
            margin: 0;
        }

        .card-body {
            padding: 24px;
        }

        /* Progress List */
        .progress-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .progress-item {
            margin-bottom: 16px;
        }

        .progress-item:last-child {
            margin-bottom: 0;
        }

        .prog-head {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .prog-bar {
            height: 8px;
            background: #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
        }

        .prog-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 1s ease-in-out;
        }

        /* ===== TABLE SECTION ===== */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        thead th {
            text-align: left;
            padding: 16px 24px;
            background: #f9fafb;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 800;
            border-bottom: 1px solid var(--line);
        }

        tbody td {
            padding: 16px 24px;
            vertical-align: middle;
            border-bottom: 1px solid var(--line);
            font-size: 0.9rem;
            color: var(--ink);
            font-weight: 600;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status-pill {
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .st-pending {
            background: #fff7ed;
            color: #c2410c;
        }

        .st-process {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .st-done {
            background: #ecfdf5;
            color: #047857;
        }

        .st-fail {
            background: #fef2f2;
            color: #b91c1c;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 32px;
            height: 32px;
            background: var(--brand-soft);
            color: var(--brand-dark);
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 0.8rem;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <div class="dashboard-container">

        {{-- Header --}}
        <div class="dashboard-header">
            <div class="page-title">
                <h1>Dashboard Overview</h1>
                <p>Ringkasan aktivitas sistem pengelolaan sampah hari ini.</p>
            </div>
            <div class="date-badge">
                <i class="fa-regular fa-calendar"></i> {{ now()->format('d F Y') }}
            </div>
        </div>

        {{-- QUICK STATS --}}
        <div class="quick-stats-grid">
            <div class="stat-card st-primary">
                <div class="stat-head">
                    <div class="stat-label">Total Pembelian</div>
                    <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
                </div>
                <div class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="stat-sub">
                    <span class="stat-trend trend-up"><i class="fa-solid fa-arrow-trend-up"></i>
                        +{{ number_format($todayRevenue, 0, ',', '.') }}</span>
                    <span>hari ini</span>
                </div>
            </div>

            <div class="stat-card st-blue">
                <div class="stat-head">
                    <div class="stat-label">Total Setoran</div>
                    <div class="stat-icon"><i class="fa-solid fa-truck-fast"></i></div>
                </div>
                <div class="stat-value">{{ number_format($totalSetoran) }}</div>
                <div class="stat-sub">
                    <span class="stat-trend trend-up">+{{ $todaySetoran }}</span>
                    <span>permintaan baru hari ini</span>
                </div>
            </div>

            <div class="stat-card st-purple">
                <div class="stat-head">
                    <div class="stat-label">Total Pengguna</div>
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                </div>
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-sub">
                    <span>{{ $totalNasabah }} Nasabah, {{ $totalPetugas }} Petugas</span>
                </div>
            </div>

            <div class="stat-card st-orange">
                <div class="stat-head">
                    <div class="stat-label">Data Sampah</div>
                    <div class="stat-icon"><i class="fa-solid fa-recycle"></i></div>
                </div>
                <div class="stat-value">{{ $totalJenisSampah }}</div>
                <div class="stat-sub">
                    <span>Terbagi dalam {{ $totalKategori }} Kategori Master</span>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT GRID --}}
        <div class="main-grid">

            {{-- LEFT COL: CHARTS --}}
            <div style="display:flex; flex-direction:column; gap:24px;">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Distribusi Status Setoran</h3>
                    </div>
                    <div class="card-body">
                        <div style="height: 250px;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Setoran Terbaru</h3>
                        <a href="#"
                            style="font-size:0.85rem; font-weight:700; color:var(--brand); text-decoration:none;">Lihat
                            Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nasabah</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSetoran as $row)
                                    <tr>
                                        <td style="font-family:monospace;">#{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <div class="avatar">{{ substr($row->user->name ?? 'U', 0, 1) }}</div>
                                                <div>{{ Str::limit($row->user->name ?? 'User', 15) }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $cls = match ($row->status) {
                                                    'pending' => 'st-pending',
                                                    'dijemput', 'proses' => 'st-process',
                                                    'selesai' => 'st-done',
                                                    default => 'st-fail',
                                                };
                                            @endphp
                                            <span
                                                class="status-pill {{ $cls }}">{{ ucfirst($row->status) }}</span>
                                        </td>
                                        <td style="color:var(--muted); font-size:0.8rem;">
                                            {{ $row->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align:center; padding:30px; color:var(--muted);">
                                            Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- RIGHT COL: PROGRESS & TOP --}}
            <div style="display:flex; flex-direction:column; gap:24px;">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Progress Hari Ini</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $totalAll = array_sum($setoranStatus);
                            $colors = [
                                'pending' => '#f59e0b',
                                'dijemput' => '#3b82f6',
                                'selesai' => '#10b981',
                                'ditolak' => '#ef4444',
                            ];
                            $labels = [
                                'pending' => 'Menunggu',
                                'dijemput' => 'Dijemput',
                                'selesai' => 'Selesai',
                                'ditolak' => 'Ditolak',
                            ];
                        @endphp

                        <ul class="progress-list">
                            @foreach ($setoranStatus as $key => $val)
                                @php $pct = $totalAll > 0 ? ($val/$totalAll)*100 : 0; @endphp
                                <li class="progress-item">
                                    <div class="prog-head">
                                        <span>{{ $labels[$key] ?? ucfirst($key) }}</span>
                                        <span>{{ $val }} <span
                                                style="color:var(--muted); font-weight:400;">({{ round($pct) }}%)</span></span>
                                    </div>
                                    <div class="prog-bar">
                                        <div class="prog-fill"
                                            style="width:{{ $pct }}%; background:{{ $colors[$key] ?? '#ccc' }};">
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top Kategori</h3>
                    </div>
                    <div class="card-body" style="padding:0;">
                        @foreach ($topKategori as $cat)
                            <div
                                style="padding:16px 24px; border-bottom:1px solid var(--line); display:flex; justify-content:space-between; align-items:center;">
                                <div style="display:flex; gap:12px; align-items:center;">
                                    <div
                                        style="width:36px; height:36px; background:var(--bg); border-radius:8px; display:grid; place-items:center; color:var(--muted);">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div>
                                        <div style="font-weight:700; color:var(--ink);">{{ $cat->nama_sampah }}</div>
                                        <div style="font-size:0.8rem; color:var(--muted);">{{ $cat->jumlah_transaksi }}
                                            Transaksi</div>
                                    </div>
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-weight:800; color:var(--brand-dark);">Rp
                                        {{ number_format($cat->total_pendapatan ?? 0, 0, ',', '.') }}</div>
                                    <div style="font-size:0.75rem; color:var(--muted);">Total</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        const ctx = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Menunggu', 'Dijemput', 'Selesai', 'Ditolak'],
                datasets: [{
                    data: [
                        {{ $setoranStatus['pending'] }},
                        {{ $setoranStatus['dijemput'] }},
                        {{ $setoranStatus['selesai'] }},
                        {{ $setoranStatus['ditolak'] }}
                    ],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif"
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    </script>
@endpush
