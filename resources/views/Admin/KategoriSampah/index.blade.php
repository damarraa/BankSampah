@extends('layouts.admin')

@section('title', 'Data Kategori Sampah')

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
        --danger: #ef4444;
    }

    /* ===== PAGE HEADER ===== */
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

    .btn-secondary { background: #fff; color: var(--ink); border-color: var(--line); }
    .btn-secondary:hover { border-color: var(--brand); color: var(--brand); transform: translateY(-2px); }

    /* ===== STATS ROW ===== */
    .stats-row {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;
    }
    .stat-card {
        background: var(--card); padding: 16px; border-radius: 16px; border: 1px solid var(--line);
        display: flex; align-items: center; gap: 12px;
    }
    .stat-icon {
        width: 42px; height: 42px; border-radius: 10px; background: var(--brand-soft);
        color: var(--brand-dark); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
    }
    .stat-info .val { font-size: 1.25rem; font-weight: 800; color: var(--ink); line-height: 1; }
    .stat-info .lbl { font-size: 0.75rem; color: var(--muted); font-weight: 600; margin-top: 4px; text-transform: uppercase; }

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
    .search-wrapper {
        display: flex; align-items: center; gap: 8px; background: var(--bg);
        padding: 4px 4px 4px 12px; border-radius: 12px; border: 1px solid var(--line);
    }
    .search-input {
        border: none; background: transparent; outline: none; font-weight: 600; color: var(--ink); width: 220px;
    }
    .btn-search-icon {
        width: 32px; height: 32px; background: var(--brand); color: #fff; border-radius: 8px;
        border: none; cursor: pointer; display: grid; place-items: center; transition: .2s;
    }
    .btn-search-icon:hover { background: var(--brand-dark); }

    /* Table */
    .table-responsive { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 900px; }
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
    .col-img { width: 80px; }
    .thumb-wrap {
        width: 50px; height: 50px; border-radius: 10px; overflow: hidden; border: 1px solid var(--line);
        background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #cbd5e1;
    }
    .thumb-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.2s; }
    .thumb-wrap:hover img { transform: scale(1.1); cursor: zoom-in; }

    .badge-cat {
        display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 8px;
        background: #eff6ff; color: #3b82f6; font-size: 0.75rem; font-weight: 700; border: 1px solid #dbeafe;
    }
    .price-tag { font-family: monospace; font-size: 0.95rem; color: var(--brand-dark); font-weight: 800; }

    /* Actions */
    .action-group { display: flex; gap: 8px; }
    .btn-icon {
        width: 34px; height: 34px; border-radius: 8px; display: grid; place-items: center;
        border: 1px solid transparent; transition: .2s; cursor: pointer; text-decoration: none;
    }
    .btn-edit { background: #eff6ff; color: #3b82f6; border-color: #dbeafe; }
    .btn-edit:hover { background: #3b82f6; color: #fff; }
    .btn-del { background: #fef2f2; color: #ef4444; border-color: #fee2e2; }
    .btn-del:hover { background: #ef4444; color: #fff; }

    /* Footer & Empty */
    .card-footer { padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--line); }
    .empty-state { padding: 60px 20px; text-align: center; }
    .empty-icon { font-size: 3rem; color: #cbd5e1; margin-bottom: 10px; }

    /* Alert Float */
    .alert-float {
        position: fixed; top: 20px; right: 20px; z-index: 9999;
        background: #ecfdf5; border: 1px solid #10b981; color: #065f46;
        padding: 16px 20px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        display: flex; align-items: center; gap: 12px; font-weight: 700;
        animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>
@endpush

@section('content')
<div style="padding-bottom: 60px;">

    {{-- Alert Success --}}
    @if(session('success'))
    <div class="alert-float" id="successAlert">
        <i class="fa-solid fa-circle-check" style="font-size:1.2rem"></i>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    {{-- HEADER --}}
    <div class="page-header">
        <div class="page-title">
            <h1><i class="fa-solid fa-recycle" style="color:var(--brand)"></i> Data Sampah</h1>
            <p>Atur jenis sampah, harga satuan, dan gambar referensi.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('master_kategori_sampah.index') }}" class="btn-action btn-secondary">
                <i class="fa-solid fa-layer-group"></i> Master Kategori
            </a>
            <a href="{{ route('kategori_sampah.create') }}" class="btn-action btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah Data
            </a>
        </div>
    </div>

    {{-- STATS OVERVIEW --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-box-open"></i></div>
            <div class="stat-info">
                <div class="val">{{ $items->total() }}</div>
                <div class="lbl">Total Item</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff; color:#3b82f6"><i class="fa-solid fa-tag"></i></div>
            <div class="stat-info">
                <div class="val">{{ $items->groupBy('master_kategori_id')->count() }}</div>
                <div class="lbl">Kategori Terpakai</div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="content-card">
        {{-- Toolbar --}}
        <div class="toolbar">
            <div style="font-weight:800; color:var(--ink); font-size:1.1rem;">Daftar Item Sampah</div>

            <form method="GET" action="{{ route('kategori_sampah.index') }}">
                <div class="search-wrapper">
                    <input type="text" name="q" value="{{ $q }}" class="search-input" placeholder="Cari nama sampah..." autocomplete="off">
                    <button type="submit" class="btn-search-icon"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>

            @if($q)
                <a href="{{ route('kategori_sampah.index') }}" style="font-size:0.85rem; font-weight:600; color:var(--danger); text-decoration:none;">
                    <i class="fa-solid fa-xmark"></i> Reset
                </a>
            @endif
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width:60px; text-align:center;">#</th>
                        <th class="col-img">Gambar</th>
                        <th>Nama Sampah</th>
                        <th>Kategori</th>
                        <th>Harga Satuan</th>
                        <th>Satuan</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td style="text-align:center; color:var(--muted);">
                            {{ $loop->iteration + ($items->currentPage()-1) * $items->perPage() }}
                        </td>

                        <td>
                            <div class="thumb-wrap">
                                @if($item->gambar_sampah)
                                    <img src="{{ asset('storage/'.$item->gambar_sampah) }}" alt="img" onclick="showPreview(this.src)">
                                @else
                                    <i class="fa-regular fa-image" style="font-size:1.2rem;"></i>
                                @endif
                            </div>
                        </td>

                        <td>
                            <div style="font-weight:700; color:var(--ink);">{{ $item->nama_sampah }}</div>
                            @if($item->deskripsi)
                                <div style="font-size:0.8rem; color:var(--muted); margin-top:2px;">{{ Str::limit($item->deskripsi, 40) }}</div>
                            @endif
                        </td>

                        <td>
                            <span class="badge-cat">
                                <i class="fa-solid fa-layer-group" style="font-size:0.7rem;"></i>
                                {{ $item->masterKategori->nama_kategori ?? '-' }}
                            </span>
                        </td>

                        <td>
                            @if($item->harga_satuan)
                                <span class="price-tag">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
                            @else
                                <span style="color:var(--muted); font-size:0.85rem;">-</span>
                            @endif
                        </td>

                        <td>
                            <span style="background:var(--bg); padding:4px 8px; border-radius:6px; font-size:0.8rem; border:1px solid var(--line);">
                                {{ $item->jenis_satuan ?? '-' }}
                            </span>
                        </td>

                        <td>
                            <div class="action-group" style="justify-content: flex-end;">
                                <a href="{{ route('kategori_sampah.edit', $item->id) }}" class="btn-icon btn-edit" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                <form action="{{ route('kategori_sampah.destroy', $item->id) }}" method="POST"
                                      onsubmit="return confirmDelete(event, '{{ addslashes($item->nama_sampah) }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon btn-del" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fa-solid fa-box-open"></i></div>
                                <h3 style="font-weight:800; color:var(--ink); margin-bottom:6px;">Data Kosong</h3>
                                <p style="color:var(--muted); font-size:0.9rem;">Belum ada data sampah. Silakan tambah data baru.</p>
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
            <div>{{ $items->links() }}</div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Auto hide alert
    setTimeout(() => {
        const alert = document.getElementById('successAlert');
        if(alert) {
            alert.style.transition = "opacity 0.5s, transform 0.5s";
            alert.style.opacity = '0'; alert.style.transform = 'translateX(100%)';
            setTimeout(() => alert.remove(), 500);
        }
    }, 4000);

    // Image Preview Modal
    function showPreview(src) {
        const modal = document.createElement('div');
        modal.style.cssText = `position:fixed; inset:0; background:rgba(0,0,0,0.8); z-index:9999; display:flex; align-items:center; justify-content:center; cursor:zoom-out;`;
        modal.innerHTML = `<img src="${src}" style="max-width:90%; max-height:90%; border-radius:12px; box-shadow:0 20px 50px rgba(0,0,0,0.5);">`;
        modal.onclick = () => modal.remove();
        document.body.appendChild(modal);
    }

    // Modern Confirm Delete
    function confirmDelete(e, name) {
        e.preventDefault();
        const modal = document.createElement('div');
        modal.style.cssText = `position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:9999; display:flex; align-items:center; justify-content:center; padding:20px; animation:fadeIn 0.2s ease-out;`;

        modal.innerHTML = `
            <div style="background:white; border-radius:20px; width:100%; max-width:400px; padding:24px; box-shadow:0 20px 50px rgba(0,0,0,0.2); transform:scale(0.95); animation:popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;">
                <div style="width:50px; height:50px; background:#fee2e2; border-radius:50%; color:#ef4444; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin:0 auto 16px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 style="text-align:center; margin:0 0 8px; color:#0f172a; font-weight:800;">Hapus Data?</h3>
                <p style="text-align:center; margin:0 0 24px; color:#64748b; font-size:0.9rem; line-height:1.5;">
                    Anda akan menghapus <b>"${name}"</b>.<br>Data ini akan hilang permanen dari database.
                </p>
                <div style="display:flex; gap:12px;">
                    <button id="btnCancel" style="flex:1; padding:12px; border-radius:12px; border:1px solid #e2e8f0; background:white; color:#0f172a; font-weight:700; cursor:pointer; transition:.2s;">Batal</button>
                    <button id="btnConfirm" style="flex:1; padding:12px; border-radius:12px; border:none; background:#ef4444; color:white; font-weight:700; cursor:pointer; transition:.2s; box-shadow:0 4px 12px rgba(239,68,68,0.3);">Ya, Hapus</button>
                </div>
            </div>
            <style>
                @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
                @keyframes popIn { from { transform:scale(0.9); opacity:0; } to { transform:scale(1); opacity:1; } }
            </style>
        `;

        document.body.appendChild(modal);
        modal.querySelector('#btnCancel').onclick = () => modal.remove();
        modal.querySelector('#btnConfirm').onclick = () => { e.target.submit(); modal.querySelector('#btnConfirm').innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>'; };
        modal.onclick = (evt) => { if(evt.target === modal) modal.remove(); };
        return false;
    }
</script>
@endpush
