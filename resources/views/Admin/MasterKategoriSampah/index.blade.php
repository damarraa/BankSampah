@extends('layouts.admin')

@section('title', 'Master Kategori Sampah')

@push('styles')
    <style>
        :root {
            --brand: #10b981;
            --brand-dark: #059669;
            --brand-soft: #ecfdf5;
            --danger: #ef4444;
            --danger-soft: #fef2f2;
            --bg: #f8fafc;
            --card: #ffffff;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --radius: 16px;
        }

        /* Container adjustments */
        .master-page {
            padding-bottom: 60px;
        }

        /* ===== HEADER SECTION ===== */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 24px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .page-title h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--ink);
            margin: 0;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .btn-add {
            background: var(--brand);
            color: #fff;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: .2s;
            border: 1px solid var(--brand);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .btn-add:hover {
            background: var(--brand-dark);
            transform: translateY(-2px);
        }

        /* ===== STATS ROW ===== */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--card);
            padding: 20px;
            border-radius: 16px;
            border: 1px solid var(--line);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: .2s;
        }

        .stat-card:hover {
            border-color: var(--brand);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--brand-soft);
            color: var(--brand-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-info h4 {
            margin: 0;
            font-size: 0.8rem;
            color: var(--muted);
            text-transform: uppercase;
            font-weight: 700;
        }

        .stat-info .val {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--ink);
            line-height: 1.2;
        }

        /* ===== MAIN CONTENT CARD ===== */
        .content-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        /* Toolbar */
        .toolbar {
            padding: 20px;
            border-bottom: 1px solid var(--line);
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .search-group {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg);
            padding: 4px;
            border-radius: 12px;
            border: 1px solid var(--line);
        }

        .search-input {
            border: none;
            background: transparent;
            padding: 8px 12px;
            outline: none;
            font-weight: 600;
            color: var(--ink);
            width: 250px;
            font-size: 0.9rem;
        }

        .btn-search {
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: 8px;
            width: 36px;
            height: 36px;
            cursor: pointer;
            display: grid;
            place-items: center;
            transition: .2s;
        }

        .btn-search:hover {
            background: var(--brand-dark);
        }

        .btn-reset {
            padding: 0 12px;
            color: var(--muted);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .btn-reset:hover {
            color: var(--danger);
        }

        /* Table Styling */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f9fafb;
            border-bottom: 1px solid var(--line);
        }

        th {
            text-align: left;
            padding: 16px 24px;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        td {
            padding: 16px 24px;
            vertical-align: middle;
            border-bottom: 1px solid var(--line);
            color: var(--ink);
            font-size: 0.9rem;
            font-weight: 600;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #f8fafc;
        }

        /* Custom Columns */
        .col-id {
            width: 60px;
            text-align: center;
            color: var(--muted);
        }

        .category-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cat-icon {
            width: 32px;
            height: 32px;
            background: var(--brand-soft);
            color: var(--brand-dark);
            border-radius: 8px;
            display: grid;
            place-items: center;
            font-size: 0.9rem;
        }

        .desc-cell {
            max-width: 350px;
            color: var(--muted);
            font-size: 0.85rem;
            line-height: 1.5;
        }

        .empty-desc {
            font-style: italic;
            color: #cbd5e1;
        }

        /* Action Buttons */
        .action-group {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .btn-action {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            border: 1px solid transparent;
            transition: .2s;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-edit {
            background: #eff6ff;
            color: #3b82f6;
            border-color: #dbeafe;
        }

        .btn-edit:hover {
            background: #3b82f6;
            color: #fff;
        }

        .btn-del {
            background: #fef2f2;
            color: #ef4444;
            border-color: #fee2e2;
        }

        .btn-del:hover {
            background: #ef4444;
            color: #fff;
        }

        /* Footer & Empty State */
        .card-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-icon {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        /* Alert */
        .alert-float {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            background: #ecfdf5;
            border: 1px solid #10b981;
            color: #065f46;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
@endpush

@section('content')
    <div class="master-page">

        {{-- ALERT SUCCESS --}}
        @if (session('success'))
            <div class="alert-float" id="successAlert">
                <i class="fa-solid fa-circle-check" style="font-size:1.2rem;"></i>
                <div>
                    <div style="font-size:0.8rem; text-transform:uppercase; opacity:0.8;">Sukses</div>
                    <div>{{ session('success') }}</div>
                </div>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fa-solid fa-layer-group" style="color:var(--brand)"></i> Master Kategori</h1>
                <p>Kelola data kategori sampah utama untuk referensi sistem.</p>
            </div>
            <a href="{{ route('master_kategori_sampah.create') }}" class="btn-add">
                <i class="fa-solid fa-plus"></i> Tambah Baru
            </a>
        </div>

        {{-- STATS ROW --}}
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-database"></i></div>
                <div class="stat-info">
                    <h4>Total Kategori</h4>
                    <div class="val">{{ $items->total() }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#eff6ff; color:#3b82f6;"><i class="fa-solid fa-file-signature"></i>
                </div>
                <div class="stat-info">
                    <h4>Dengan Deskripsi</h4>
                    <div class="val">{{ $itemsWithDescription ?? $items->whereNotNull('deskripsi')->count() }}</div>
                </div>
            </div>
            {{-- Slot kosong bisa dipakai untuk stat lain --}}
        </div>

        {{-- MAIN TABLE CARD --}}
        <div class="content-card">
            {{-- Toolbar --}}
            <div class="toolbar">
                <div style="font-weight:800; color:var(--ink); font-size:1.1rem;">
                    Daftar Data
                </div>

                <form method="GET" action="{{ route('master_kategori_sampah.index') }}">
                    <div class="search-group">
                        <input type="text" name="q" value="{{ $q }}" class="search-input"
                            placeholder="Cari nama kategori..." autocomplete="off">
                        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </form>
                @if ($q)
                    <a href="{{ route('master_kategori_sampah.index') }}" class="btn-reset">
                        <i class="fa-solid fa-xmark"></i> Reset Filter
                    </a>
                @endif
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th class="col-id">#</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td class="col-id">{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}
                                </td>

                                <td>
                                    <div class="category-cell">
                                        <div class="cat-icon"><i class="fa-solid fa-tag"></i></div>
                                        <span>{{ $item->nama_kategori }}</span>
                                    </div>
                                </td>

                                <td>
                                    <div class="desc-cell {{ !$item->deskripsi ? 'empty-desc' : '' }}">
                                        {{ $item->deskripsi ? Str::limit($item->deskripsi, 80) : 'Tidak ada deskripsi' }}
                                    </div>
                                </td>

                                <td>
                                    <div class="action-group">
                                        <a href="{{ route('master_kategori_sampah.edit', $item->id) }}"
                                            class="btn-action btn-edit" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <form action="{{ route('master_kategori_sampah.destroy', $item->id) }}"
                                            method="POST"
                                            onsubmit="return confirmDelete(event, '{{ addslashes($item->nama_kategori) }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action btn-del" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fa-regular fa-folder-open"></i></div>
                                        <h3 style="font-weight:800; color:var(--ink); margin-bottom:8px;">Data Kosong</h3>
                                        <p style="color:var(--muted); font-size:0.9rem;">
                                            @if ($q)
                                                Tidak ditemukan hasil untuk "{{ $q }}".
                                            @else
                                                Belum ada master kategori yang ditambahkan.
                                            @endif
                                        </p>
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
                    Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} dari {{ $items->total() }}
                    data
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
            if (alert) {
                alert.style.transition = "opacity 0.5s, transform 0.5s";
                alert.style.opacity = '0';
                alert.style.transform = 'translateX(100%)';
                setTimeout(() => alert.remove(), 500);
            }
        }, 4000);

        // Modern Confirm Delete Modal
        function confirmDelete(e, name) {
            e.preventDefault();

            // Buat overlay modal secara dinamis
            const modal = document.createElement('div');
            modal.style.cssText = `
            position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
            z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;
            animation: fadeIn 0.2s ease-out;
        `;

            modal.innerHTML = `
            <div style="background: white; border-radius: 20px; width: 100%; max-width: 400px; padding: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.2); transform: scale(0.95); animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;">
                <div style="width: 50px; height: 50px; background: #fee2e2; border-radius: 50%; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 16px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 style="text-align: center; margin: 0 0 8px; color: #0f172a; font-weight: 800;">Hapus Kategori?</h3>
                <p style="text-align: center; margin: 0 0 24px; color: #64748b; font-size: 0.9rem; line-height: 1.5;">
                    Anda akan menghapus <b>"${name}"</b>. <br>Data yang sudah dihapus tidak dapat dikembalikan.
                </p>
                <div style="display: flex; gap: 12px;">
                    <button id="btnCancel" style="flex: 1; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; color: #0f172a; font-weight: 700; cursor: pointer; transition: .2s;">Batal</button>
                    <button id="btnConfirm" style="flex: 1; padding: 12px; border-radius: 12px; border: none; background: #ef4444; color: white; font-weight: 700; cursor: pointer; transition: .2s; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">Ya, Hapus</button>
                </div>
            </div>
            <style>
                @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
                @keyframes popIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
            </style>
        `;

            document.body.appendChild(modal);

            modal.querySelector('#btnCancel').onclick = () => modal.remove();
            modal.querySelector('#btnConfirm').onclick = () => {
                e.target.submit(); // Submit form asli
                modal.querySelector('#btnConfirm').innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            };
            modal.onclick = (evt) => {
                if (evt.target === modal) modal.remove();
            };

            return false;
        }
    </script>
@endpush
