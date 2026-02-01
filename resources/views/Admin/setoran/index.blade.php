@extends('layouts.admin')

@section('title', 'Monitoring Setoran')

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
    .page-header {
        display: flex; justify-content: space-between; align-items: flex-end;
        margin-bottom: 24px; gap: 20px; flex-wrap: wrap;
    }
    .page-title h1 {
        font-size: 1.75rem; font-weight: 800; color: var(--ink); margin: 0;
        letter-spacing: -0.5px; display: flex; align-items: center; gap: 10px;
    }
    .page-title p { margin: 6px 0 0; color: var(--muted); font-size: 0.95rem; }

    .btn-action {
        padding: 10px 18px; border-radius: 12px; font-weight: 700; font-size: 0.9rem;
        display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
        transition: .2s; border: 1px solid transparent; box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .btn-primary { background: var(--brand); color: #fff; border-color: var(--brand); }
    .btn-primary:hover { background: var(--brand-dark); transform: translateY(-2px); }

    /* ===== STATS ROW ===== */
    .stats-row {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;
    }
    .stat-card {
        background: var(--card); padding: 16px; border-radius: 16px; border: 1px solid var(--line);
        display: flex; align-items: center; gap: 12px; transition: .2s;
    }
    .stat-card:hover { transform: translateY(-2px); border-color: var(--brand); }

    .stat-icon {
        width: 42px; height: 42px; border-radius: 10px; background: var(--bg-icon, var(--brand-soft));
        color: var(--text-icon, --brand-dark); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
    }
    .stat-info .val { font-size: 1.4rem; font-weight: 800; color: var(--ink); line-height: 1; }
    .stat-info .lbl { font-size: 0.75rem; color: var(--muted); font-weight: 600; margin-top: 4px; text-transform: uppercase; }

    /* ===== FILTER TABS ===== */
    .filter-tabs {
        display: flex; gap: 8px; margin-bottom: 16px; overflow-x: auto; padding-bottom: 4px;
    }
    .tab-btn {
        padding: 8px 16px; border-radius: 50px; background: #fff; border: 1px solid var(--line);
        color: var(--muted); font-size: 0.85rem; font-weight: 700; cursor: pointer; white-space: nowrap; transition: .2s;
    }
    .tab-btn:hover { border-color: var(--brand); color: var(--brand-dark); }
    .tab-btn.active { background: var(--brand); color: #fff; border-color: var(--brand); box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); }
    .tab-count {
        background: rgba(0,0,0,0.1); padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; margin-left: 6px;
    }
    .tab-btn.active .tab-count { background: rgba(255,255,255,0.25); color: #fff; }

    /* ===== MAIN CARD ===== */
    .content-card {
        background: var(--card); border-radius: 20px; border: 1px solid var(--line);
        overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }

    /* Toolbar */
    .toolbar {
        padding: 20px; border-bottom: 1px solid var(--line); background: #fff;
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
    }

    .filter-group { display: flex; gap: 10px; flex-wrap: wrap; }
    .filter-select {
        padding: 8px 12px; border-radius: 10px; border: 1px solid var(--line); background: #f8fafc;
        font-size: 0.85rem; font-weight: 600; color: var(--ink); outline: none; cursor: pointer;
    }
    .filter-select:focus { border-color: var(--brand); background: #fff; }

    .search-wrapper {
        display: flex; align-items: center; gap: 8px; background: var(--bg);
        padding: 6px 6px 6px 12px; border-radius: 12px; border: 1px solid var(--line);
    }
    .search-input {
        border: none; background: transparent; outline: none; font-weight: 600; color: var(--ink); width: 200px; font-size: 0.9rem;
    }
    .btn-search-icon {
        width: 32px; height: 32px; background: var(--brand); color: #fff; border-radius: 8px;
        border: none; cursor: pointer; display: grid; place-items: center; transition: .2s;
    }
    .btn-search-icon:hover { background: var(--brand-dark); }

    /* Table */
    .table-responsive { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 1000px; }
    thead th {
        text-align: left; padding: 16px 20px; background: #f9fafb;
        font-size: 0.75rem; text-transform: uppercase; color: var(--muted); font-weight: 800; letter-spacing: 0.5px;
        border-bottom: 1px solid var(--line);
    }
    tbody td {
        padding: 16px 20px; vertical-align: middle; border-bottom: 1px solid var(--line);
        color: var(--ink); font-size: 0.9rem; font-weight: 600;
    }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #f8fafc; }

    /* Custom Cells */
    .user-info { display: flex; align-items: center; gap: 10px; }
    .avatar {
        width: 36px; height: 36px; background: var(--brand-soft); color: var(--brand-dark);
        border-radius: 50%; display: grid; place-items: center; font-weight: 700; font-size: 0.85rem;
    }
    .user-text div { line-height: 1.3; }
    .user-sub { font-size: 0.75rem; color: var(--muted); }

    .badge {
        display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .badge-pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
    .badge-proses { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
    .badge-selesai { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
    .badge-batal { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

    .price-val { font-family: monospace; font-size: 0.95rem; font-weight: 700; color: var(--brand-dark); }

    /* Actions */
    .btn-sm {
        padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; text-decoration: none;
        display: inline-flex; align-items: center; gap: 6px; border: 1px solid transparent; transition: .2s;
    }
    .btn-view { background: #fff; border-color: var(--line); color: var(--ink); }
    .btn-view:hover { border-color: var(--brand); color: var(--brand); }
    .btn-process { background: var(--brand-soft); color: var(--brand-dark); border-color: rgba(16,185,129,0.2); }
    .btn-process:hover { background: var(--brand); color: #fff; }

    /* Footer & Empty */
    .card-footer { padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--line); }
    .empty-state { padding: 60px 20px; text-align: center; }
    .empty-icon { font-size: 3rem; color: #cbd5e1; margin-bottom: 10px; }
</style>
@endpush

@section('content')
<div style="padding-bottom: 60px;">

    {{-- HEADER --}}
    <div class="page-header">
        <div class="page-title">
            <h1><i class="fa-solid fa-list-check" style="color:var(--brand)"></i> Monitoring Setoran</h1>
            <p>Pantau status transaksi dan kinerja petugas lapangan.</p>
        </div>
        <a href="{{ route('admin.map') }}" class="btn-action btn-primary">
            <i class="fa-solid fa-map-location-dot"></i> Live Map
        </a>
    </div>

    {{-- STATS OVERVIEW --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-box-archive"></i></div>
            <div class="stat-info">
                <div class="val">{{ $items->total() }}</div>
                <div class="lbl">Total Transaksi</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="--bg-icon:#fff7ed; --text-icon:#c2410c"><i class="fa-solid fa-clock"></i></div>
            <div class="stat-info">
                <div class="val">{{ $countPending ?? 0 }}</div>
                <div class="lbl">Menunggu</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="--bg-icon:#eff6ff; --text-icon:#1d4ed8"><i class="fa-solid fa-spinner"></i></div>
            <div class="stat-info">
                <div class="val">{{ $countProcess ?? 0 }}</div>
                <div class="lbl">Sedang Proses</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-coins"></i></div>
            <div class="stat-info">
                <div class="val">Rp {{ number_format($totalValue ?? 0, 0, ',', '.') }}</div>
                <div class="lbl">Estimasi Nilai</div>
            </div>
        </div>
    </div>

    {{-- FILTER TABS --}}
    <div class="filter-tabs">
        <button class="tab-btn {{ !$status ? 'active' : '' }}" onclick="filterStatus('')">
            Semua <span class="tab-count">{{ $totalAll ?? 0 }}</span>
        </button>
        <button class="tab-btn {{ $status == 'menunggu' ? 'active' : '' }}" onclick="filterStatus('menunggu')">
            Menunggu <span class="tab-count">{{ $countPending ?? 0 }}</span>
        </button>
        <button class="tab-btn {{ $status == 'diproses' ? 'active' : '' }}" onclick="filterStatus('diproses')">
            Diproses <span class="tab-count">{{ $countProcess ?? 0 }}</span>
        </button>
        <button class="tab-btn {{ $status == 'selesai' ? 'active' : '' }}" onclick="filterStatus('selesai')">
            Selesai <span class="tab-count">{{ $countDone ?? 0 }}</span>
        </button>
        <button class="tab-btn {{ $status == 'dibatalkan' ? 'active' : '' }}" onclick="filterStatus('dibatalkan')">
            Dibatalkan <span class="tab-count">{{ $countCancel ?? 0 }}</span>
        </button>
    </div>

    {{-- MAIN TABLE --}}
    <div class="content-card">
        {{-- Toolbar --}}
        <div class="toolbar">
            <form id="filterForm" method="GET" action="{{ route('admin.setoran.index') }}" class="filter-group">
                <input type="hidden" name="status" id="statusInput" value="{{ $status }}">

                <select name="metode" class="filter-select" onchange="this.form.submit()">
                    <option value="">Semua Metode</option>
                    <option value="jemput" {{ $metode == 'jemput' ? 'selected' : '' }}>Jemput</option>
                    <option value="antar" {{ $metode == 'antar' ? 'selected' : '' }}>Antar Sendiri</option>
                </select>

                <select name="petugas" class="filter-select" onchange="this.form.submit()">
                    <option value="">Semua Petugas</option>
                    @foreach($petugasList as $p)
                        <option value="{{ $p->id }}" {{ $petugasId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </form>

            <form method="GET" action="{{ route('admin.setoran.index') }}">
                @if($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
                <div class="search-wrapper">
                    <input type="text" name="q" value="{{ $q }}" class="search-input" placeholder="Cari ID / Nama User..." autocomplete="off">
                    <button type="submit" class="btn-search-icon"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nasabah</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Petugas</th>
                        <th>Estimasi</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td style="font-family:monospace; color:var(--muted);">#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</td>

                        <td>
                            <div class="user-info">
                                <div class="avatar">{{ substr($item->user->name ?? 'U', 0, 1) }}</div>
                                <div class="user-text">
                                    <div style="font-weight:700;">{{ $item->user->name ?? '-' }}</div>
                                    <div class="user-sub">{{ $item->created_at->format('d M Y, H:i') }}</div>
                                </div>
                            </div>
                        </td>

                        <td>
                            @if($item->metode == 'jemput')
                                <div style="display:flex; align-items:center; gap:6px; font-size:0.85rem; font-weight:600; color:#0f172a;">
                                    <i class="fa-solid fa-truck-fast" style="color:var(--brand)"></i> Jemput
                                </div>
                            @else
                                <div style="display:flex; align-items:center; gap:6px; font-size:0.85rem; font-weight:600; color:#64748b;">
                                    <i class="fa-solid fa-person-walking-luggage"></i> Antar
                                </div>
                            @endif
                        </td>

                        <td>
                            @php
                                $badgeClass = match($item->status) {
                                    'menunggu' => 'badge-pending',
                                    'diproses' => 'badge-proses',
                                    'selesai' => 'badge-selesai',
                                    default => 'badge-batal'
                                };
                                $icon = match($item->status) {
                                    'menunggu' => 'fa-clock',
                                    'diproses' => 'fa-spinner fa-spin',
                                    'selesai' => 'fa-check-circle',
                                    default => 'fa-times-circle'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                <i class="fa-solid {{ $icon }}"></i> {{ ucfirst($item->status) }}
                            </span>
                        </td>

                        <td>
                            @if($item->petugas)
                                <div style="font-size:0.85rem; font-weight:600;">{{ $item->petugas->name }}</div>
                            @else
                                <span style="color:var(--muted); font-size:0.8rem; font-style:italic;">-</span>
                            @endif
                        </td>

                        <td>
                            <span class="price-val">Rp {{ number_format($item->estimasi_total, 0, ',', '.') }}</span>
                        </td>

                        <td style="text-align:right;">
                            <div style="display:flex; justify-content:flex-end; gap:8px;">
                                @if($item->status == 'menunggu')
                                    <a href="{{ route('admin.setoran.edit', $item->id) }}" class="btn-sm btn-process">
                                        <i class="fa-solid fa-bolt"></i> Proses
                                    </a>
                                @endif
                                <a href="{{ route('admin.setoran.show', $item->id) }}" class="btn-sm btn-view">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fa-solid fa-filter-circle-xmark"></i></div>
                                <h3 style="font-weight:800; color:var(--ink); margin-bottom:6px;">Data Tidak Ditemukan</h3>
                                <p style="color:var(--muted); font-size:0.9rem;">Coba ubah filter status atau kata kunci pencarian.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="card-footer">
            <div style="font-size:0.85rem; color:var(--muted); font-weight:600;">
                Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} data
            </div>
            <div>{{ $items->appends(request()->query())->links() }}</div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function filterStatus(status) {
        document.getElementById('statusInput').value = status;
        document.getElementById('filterForm').submit();
    }
</script>
@endpush
