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

        /* ===== HEADER ===== */
        .dashboard-header {
            display: flex; justify-content: space-between; align-items: flex-end;
            margin-bottom: 24px; flex-wrap: wrap; gap: 16px;
        }
        .page-title h1 { font-size: 1.75rem; font-weight: 800; margin: 0; color: var(--ink); letter-spacing: -0.5px; }
        .page-title p { margin: 4px 0 0; color: var(--muted); font-size: 0.95rem; }
        .date-badge {
            background: #fff; padding: 8px 16px; border-radius: 50px; border: 1px solid var(--line);
            font-weight: 700; color: var(--ink); font-size: 0.9rem; display: flex; align-items: center; gap: 8px;
        }

        /* ===== MAIN STATS (3 CARDS) ===== */
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px; margin-bottom: 32px;
        }
        .stat-card {
            background: var(--card); padding: 24px; border-radius: var(--radius);
            border: 1px solid var(--line); position: relative; overflow: hidden;
            display: flex; flex-direction: column; justify-content: space-between;
            min-height: 160px; transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }

        .stat-icon {
            width: 48px; height: 48px; border-radius: 12px; display: grid; place-items: center;
            font-size: 1.5rem; margin-bottom: 16px;
        }
        .stat-label { font-size: 0.9rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 2rem; font-weight: 800; color: var(--ink); margin-top: 4px; letter-spacing: -1px; }
        .stat-desc { font-size: 0.85rem; color: var(--muted); margin-top: 8px; display: flex; align-items: center; gap: 6px; }

        /* Color Variants */
        .st-red { --c: #ef4444; --bg: #fef2f2; }
        .st-green { --c: #10b981; --bg: #ecfdf5; }
        .st-blue { --c: #3b82f6; --bg: #eff6ff; }

        .btn-topup {
            position: absolute; top: 20px; right: 20px;
            background: #eff6ff; color: #3b82f6; border: none; padding: 8px 14px;
            border-radius: 8px; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: 0.2s;
        }
        .btn-topup:hover { background: #dbeafe; }

        /* ===== SECONDARY GRID ===== */
        .main-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px; }
        @media (max-width: 1024px) { .main-grid { grid-template-columns: 1fr; } }

        .card {
            background: var(--card); border: 1px solid var(--line); border-radius: 20px; overflow: hidden;
        }
        .card-header { padding: 20px 24px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; }
        .card-title { font-size: 1.1rem; font-weight: 800; color: var(--ink); margin: 0; }
        .card-body { padding: 24px; }

        /* Progress List */
        .progress-list { list-style: none; padding: 0; margin: 0; }
        .progress-item { margin-bottom: 16px; }
        .prog-head { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 0.9rem; font-weight: 600; }
        .prog-bar { height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
        .prog-fill { height: 100%; border-radius: 10px; }

        /* Table */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { text-align: left; padding: 16px 24px; background: #f9fafb; font-size: 0.75rem; text-transform: uppercase; color: var(--muted); font-weight: 800; border-bottom: 1px solid var(--line); }
        td { padding: 16px 24px; border-bottom: 1px solid var(--line); font-size: 0.9rem; color: var(--ink); font-weight: 600; }
        .status-pill { padding: 4px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; display: inline-flex; }
        .st-pending { background: #fff7ed; color: #c2410c; }
        .st-process { background: #eff6ff; color: #1d4ed8; }
        .st-done { background: #ecfdf5; color: #047857; }
        .st-fail { background: #fef2f2; color: #b91c1c; }

        /* MODAL */
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; backdrop-filter: blur(2px); }
        .modal-content { background: white; width: 400px; max-width: 90%; margin: 10% auto; border-radius: 16px; padding: 24px; animation: popIn 0.3s; }
        @keyframes popIn { from{transform:scale(0.9);opacity:0} to{transform:scale(1);opacity:1} }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <div class="dashboard-container">

        {{-- HEADER --}}
        <div class="dashboard-header">
            <div class="page-title">
                <h1>Dashboard Overview</h1>
                <p>Ringkasan aktivitas dan arus kas gudang.</p>
            </div>
            <div class="date-badge">
                <i class="fa-regular fa-calendar"></i> {{ now()->translatedFormat('d F Y') }}
            </div>
        </div>

        @if(session('success'))
            <div style="background:#ecfdf5; color:#065f46; padding:12px; border-radius:12px; margin-bottom:24px; border:1px solid #a7f3d0; font-weight:600;">
                <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        {{-- 3 MAIN CARDS (BRIEF KLIEN) --}}
        <div class="stats-grid">

            {{-- 1. TOTAL PEMBELIAN (OUT) --}}
            <div class="stat-card st-red">
                <div class="stat-icon" style="background:var(--bg); color:var(--c);">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="stat-label">Total Pembelian (Keluar)</div>
                <div class="stat-value" style="color:var(--c);">
                    Rp {{ number_format($totalPembelian, 0, ',', '.') }}
                </div>
                <div class="stat-desc">
                    <i class="fa-solid fa-arrow-down"></i> Uang dibayarkan ke Penyetor
                </div>
            </div>

            {{-- 2. TOTAL PENJUALAN (IN) --}}
            <div class="stat-card st-green">
                <div class="stat-icon" style="background:var(--bg); color:var(--c);">
                    <i class="fa-solid fa-money-bill-trend-up"></i>
                </div>
                <div class="stat-label">Total Penjualan (Masuk)</div>
                <div class="stat-value" style="color:var(--c);">
                    Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                </div>
                <div class="stat-desc">
                    <i class="fa-solid fa-recycle"></i> Pengepul: {{ number_format($jualPengepul) }} | Karya: {{ number_format($jualKarya) }}
                </div>
            </div>

            {{-- 3. SALDO AKTIF --}}
            <div class="stat-card st-blue" style="border: 2px solid #3b82f6;">
                <button class="btn-topup" onclick="document.getElementById('modalSaldo').style.display='block'">
                    <i class="fa-solid fa-plus"></i> Input Modal
                </button>
                <div class="stat-icon" style="background:var(--bg); color:var(--c);">
                    <i class="fa-solid fa-vault"></i>
                </div>
                <div class="stat-label">Saldo / Kas Gudang</div>
                <div class="stat-value" style="color:var(--c);">
                    Rp {{ number_format($saldoAktif, 0, ',', '.') }}
                </div>
                <div class="stat-desc">
                    <i class="fa-solid fa-calculator"></i> (Modal + Jual) - (Beli + Ops)
                </div>
            </div>

        </div>

        {{-- CONTENT GRID --}}
        <div class="main-grid">

            {{-- LEFT: CHART & TRANSACTIONS --}}
            <div style="display:flex; flex-direction:column; gap:24px;">

                {{-- Chart Status Setoran --}}
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

                {{-- Recent Transactions --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Setoran Terbaru</h3>
                        <a href="{{ route('admin.setoran.index') }}" style="font-size:0.85rem; font-weight:700; color:var(--brand-dark); text-decoration:none;">Lihat Semua</a>
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
                                        <td style="font-family:monospace;">#{{ $row->id }}</td>
                                        <td>{{ Str::limit($row->user->name ?? 'User', 15) }}</td>
                                        <td>
                                            @php
                                                $cls = match ($row->status) {
                                                    'pending', 'menunggu' => 'st-pending',
                                                    'dijemput', 'diproses' => 'st-process',
                                                    'selesai' => 'st-done',
                                                    default => 'st-fail',
                                                };
                                            @endphp
                                            <span class="status-pill {{ $cls }}">{{ ucfirst($row->status) }}</span>
                                        </td>
                                        <td style="color:var(--muted);">{{ $row->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" style="text-align:center; padding:30px; color:var(--muted);">Belum ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- RIGHT: PROGRESS & TOP KATEGORI --}}
            <div style="display:flex; flex-direction:column; gap:24px;">

                {{-- Progress Hari Ini --}}
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Progress Hari Ini</h3></div>
                    <div class="card-body">
                        @php
                            $totalAll = array_sum($setoranStatus);
                            $colors = ['pending' => '#f59e0b', 'dijemput' => '#3b82f6', 'selesai' => '#10b981', 'ditolak' => '#ef4444'];
                            $labels = ['pending' => 'Menunggu', 'dijemput' => 'Dijemput', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'];
                        @endphp
                        <ul class="progress-list">
                            @foreach ($setoranStatus as $key => $val)
                                @php $pct = $totalAll > 0 ? ($val/$totalAll)*100 : 0; @endphp
                                <li class="progress-item">
                                    <div class="prog-head">
                                        <span>{{ $labels[$key] ?? ucfirst($key) }}</span>
                                        <span>{{ $val }} <span style="color:var(--muted); font-weight:400;">({{ round($pct) }}%)</span></span>
                                    </div>
                                    <div class="prog-bar">
                                        <div class="prog-fill" style="width:{{ $pct }}%; background:{{ $colors[$key] ?? '#ccc' }};"></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Top Kategori --}}
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Top Kategori</h3></div>
                    <div class="card-body" style="padding:0;">
                        @foreach ($topKategori as $cat)
                            <div style="padding:16px 24px; border-bottom:1px solid var(--line); display:flex; justify-content:space-between; align-items:center;">
                                <div style="display:flex; gap:12px; align-items:center;">
                                    <div style="width:36px; height:36px; background:var(--bg); border-radius:8px; display:grid; place-items:center; font-weight:700; color:var(--muted);">{{ $loop->iteration }}</div>
                                    <div>
                                        <div style="font-weight:700; color:var(--ink);">{{ $cat->nama_sampah }}</div>
                                        <div style="font-size:0.8rem; color:var(--muted);">{{ $cat->jumlah_transaksi }} Transaksi</div>
                                    </div>
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-weight:800; color:var(--brand-dark);">Rp {{ number_format($cat->total_pendapatan ?? 0, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- MODAL INPUT SALDO --}}
    <div id="modalSaldo" class="modal">
        <form action="{{ route('admin.keuangan.store') }}" method="POST" class="modal-content">
            @csrf
            <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
                <h3 style="margin:0; font-size:1.2rem; font-weight:700;">Input Modal / Keuangan</h3>
                <span onclick="document.getElementById('modalSaldo').style.display='none'" style="cursor:pointer; font-size:1.5rem;">&times;</span>
            </div>

            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Jenis Transaksi</label>
                <select name="jenis" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
                    <option value="masuk">🟢 Pemasukan (Modal Awal / Topup)</option>
                    <option value="keluar">🔴 Pengeluaran (Operasional Lain)</option>
                </select>
            </div>

            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Nominal (Rp)</label>
                <input type="number" name="nominal" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;" placeholder="Contoh: 20000">
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Keterangan</label>
                <input type="text" name="keterangan" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;" placeholder="Contoh: Modal Awal Bulan">
            </div>

            <button type="submit" style="width:100%; padding:12px; background:#3b82f6; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer;">
                Simpan Data
            </button>
        </form>
    </div>

    {{-- Script Tutup Modal --}}
    <script>
        window.onclick = function(e) {
            if(e.target == document.getElementById('modalSaldo')) {
                document.getElementById('modalSaldo').style.display = 'none';
            }
        }

        // Chart Status
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
                    legend: { position: 'right', labels: { usePointStyle: true, font: { family: "'Plus Jakarta Sans', sans-serif" } } }
                },
                cutout: '70%'
            }
        });
    </script>
@endsection
