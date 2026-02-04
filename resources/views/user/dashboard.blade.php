@extends('layouts.user')
@section('title', 'Katalog Sampah')

@php
    use Illuminate\Support\Str;

    // Grouping Kategori
    $groups = $kategori->groupBy(function ($k) {
        return $k->masterKategori?->nama_kategori ?? 'Lainnya';
    });

    $totalCount = $kategori->count();
    $totalSetoran = (int) $setoranPerTahun->sum('total');

    // Logic Pendapatan
    $totalPendapatanDisplay = $tahun
        ? optional($pendapatanPerTahun->firstWhere('tahun', $tahun))->total
        : $totalPendapatan;
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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
        }

        body {
            font-family: "Plus Jakarta Sans", sans-serif;
            background-color: var(--bg);
            padding-bottom: 100px;
            /* Ruang untuk floating bar */
        }

        .container-fluid {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px;
        }

        @media (min-width: 768px) {
            .container-fluid {
                padding: 0 32px;
            }
        }

        /* ===== HEADER HERO ===== */
        .dashboard-header {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            padding: 30px 0 80px;
            color: #fff;
            border-radius: 0 0 40px 40px;
            margin-bottom: -50px;
            position: relative;
            z-index: 1;
            box-shadow: 0 10px 30px -10px rgba(16, 185, 129, 0.5);
        }

        .hero-content {
            text-align: center;
        }

        .welcome-title {
            font-size: 1.6rem;
            font-weight: 800;
            margin: 0 0 6px;
        }

        .welcome-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            margin: 0;
        }

        /* ===== STATS GRID ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 12px;
            position: relative;
            z-index: 10;
            margin-bottom: 30px;
        }

        @media (min-width: 640px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1.2rem;
        }

        .icon-green {
            background: #ecfdf5;
            color: #10b981;
        }

        .icon-blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .stat-info h4 {
            margin: 0;
            font-size: 0.75rem;
            color: var(--muted);
            text-transform: uppercase;
            font-weight: 700;
        }

        .stat-info .value {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--ink);
        }

        /* ===== CATALOG SECTION ===== */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-wrapper {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 5px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }

        .filter-wrapper::-webkit-scrollbar {
            display: none;
        }

        .filter-btn {
            padding: 8px 16px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--muted);
            white-space: nowrap;
            cursor: pointer;
            transition: 0.2s;
        }

        .filter-btn.active {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        /* ===== PRODUCT GRID (E-COMMERCE STYLE) ===== */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            /* Mobile 2 kolom */
        }

        @media (min-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }
        }

        @media (min-width: 1024px) {
            .products-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .product-card {
            background: #fff;
            border: 2px solid transparent;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            user-select: none;
        }

        .product-card:active {
            transform: scale(0.98);
        }

        /* State Terpilih */
        .product-card.selected {
            border-color: var(--brand);
            background-color: #f0fdf4;
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.15);
        }

        /* Checkmark Overlay */
        .check-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 5;
            width: 28px;
            height: 28px;
            background: var(--brand);
            border-radius: 50%;
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 14px;
            opacity: 0;
            transform: scale(0);
            transition: 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .product-card.selected .check-icon {
            opacity: 1;
            transform: scale(1);
        }

        .product-img {
            position: relative;
            width: 100%;
            aspect-ratio: 1/1;
            background: #f1f5f9;
        }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .badge-cat {
            position: absolute;
            bottom: 8px;
            left: 8px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
            color: var(--ink);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 800;
        }

        .product-body {
            padding: 12px;
            text-align: left;
        }

        .p-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .p-price {
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--brand-dark);
        }

        .p-unit {
            font-size: 0.75rem;
            color: var(--muted);
        }

        /* Tombol status di bawah card */
        .card-action {
            margin-top: 8px;
            padding: 8px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            text-align: center;
            background: #f1f5f9;
            color: var(--muted);
            transition: 0.2s;
        }

        .product-card.selected .card-action {
            background: var(--brand);
            color: #fff;
            content: "Terpilih";
        }

        /* ===== FLOATING CART BAR ===== */
        .cart-bar {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            z-index: 1000;
            background: #111827;
            color: #fff;
            padding: 12px 20px;
            border-radius: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            transform: translateY(200%);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            max-width: 600px;
            margin: 0 auto;
        }

        .cart-bar.active {
            transform: translateY(0);
        }

        .cart-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cart-badge {
            background: var(--brand);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 0.9rem;
            color: #fff;
        }

        .cart-text div {
            font-size: 0.9rem;
            font-weight: 700;
        }

        .cart-text small {
            font-size: 0.75rem;
            opacity: 0.7;
            font-weight: 500;
        }

        .btn-checkout {
            background: var(--brand);
            color: #fff;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-checkout:hover {
            background: var(--brand-dark);
            transform: scale(1.05);
        }

        /* Empty State */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }
    </style>
@endpush

@section('content')
    <div class="dashboard-container">

        {{-- HEADER --}}
        <div class="dashboard-header">
            <div class="container-fluid">
                <div class="hero-content">
                    <h1 class="welcome-title">Katalog Sampah</h1>
                    <p class="welcome-subtitle">Pilih jenis sampah yang ingin disetor, lalu klik "Lanjut Setor".</p>
                </div>
            </div>
        </div>

        <div class="container-fluid">

            {{-- MINI STATS (Opsional, untuk konteks) --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-green"><i class="fa-solid fa-wallet"></i></div>
                    <div class="stat-info">
                        <h4>Saldo</h4>
                        <div class="value">Rp {{ number_format($totalPendapatanDisplay, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="stat-card" style="padding: 10px; background: #f8fafc; border: 1px dashed #cbd5e1;">
                    <form action="{{ route('user.dashboard') }}" method="GET"
                        style="width: 100%; display:flex; align-items:center; justify-content:space-between; padding:0 8px;">
                        <div style="font-weight:700; color:var(--muted); font-size:0.8rem;">
                            <i class="fa-solid fa-filter"></i> Periode
                        </div>
                        <select name="tahun" onchange="this.form.submit()"
                            style="border:none; background:transparent; font-weight:700; color:var(--ink); outline:none; text-align:right; cursor:pointer;">
                            <option value="">Semua</option>
                            @foreach ($listTahun as $t)
                                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            {{-- FILTER TABS --}}
            <div class="section-header">
                <div class="filter-wrapper" id="filterTabs">
                    <button class="filter-btn active" data-filter="__all__">Semua</button>
                    @foreach ($groups as $gName => $list)
                        <button class="filter-btn" data-filter="{{ Str::slug($gName) }}">{{ $gName }}</button>
                    @endforeach
                </div>
            </div>

            {{-- PRODUCTS GRID (E-COMMERCE FLOW) --}}
            <div class="products-grid" id="productsGrid">
                @forelse($kategori as $k)
                    @php
                        $gKey = Str::slug($k->masterKategori?->nama_kategori ?? 'Lainnya');
                        // Data Object untuk JS
                        $itemData = [
                            'id' => $k->id,
                            'nama' => $k->nama_sampah,
                            'harga' => $k->harga_satuan,
                            'satuan' => $k->jenis_satuan,
                        ];
                    @endphp

                    <div class="product-card" data-group="{{ $gKey }}"
                        onclick='toggleSelection(this, @json($itemData))'>

                        {{-- Checkmark Animation --}}
                        <div class="check-icon"><i class="fa-solid fa-check"></i></div>

                        <div class="product-img">
                            <span class="badge-cat">{{ $k->masterKategori?->nama_kategori ?? 'Umum' }}</span>
                            @if ($k->gambar_sampah)
                                <img src="{{ asset('storage/' . $k->gambar_sampah) }}" alt="{{ $k->nama_sampah }}"
                                    loading="lazy">
                            @else
                                <div style="width:100%; height:100%; display:grid; place-items:center; color:#cbd5e1;">
                                    <i class="fa-regular fa-image" style="font-size:2rem;"></i>
                                </div>
                            @endif
                        </div>

                        <div class="product-body">
                            <div class="p-title">{{ $k->nama_sampah }}</div>
                            <div>
                                <span
                                    class="p-price">{{ $k->harga_satuan ? 'Rp ' . number_format($k->harga_satuan, 0, ',', '.') : 'Gratis' }}</span>
                                <span class="p-unit">/ {{ $k->jenis_satuan ?? 'unit' }}</span>
                            </div>

                            {{-- Dynamic Button Text handled by JS/CSS --}}
                            <div class="card-action">
                                <span class="txt-add"><i class="fa-solid fa-plus"></i> Tambah</span>
                                <span class="txt-added" style="display:none">Terpilih</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fa-solid fa-box-open"></i></div>
                        <h3>Belum ada data</h3>
                        <p>Admin belum menambahkan katalog sampah.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    {{-- FLOATING CART BAR --}}
    <div class="cart-bar" id="cartBar">
        <div class="cart-info">
            <div class="cart-badge" id="cartCount">0</div>
            <div class="cart-text">
                <div>Item Terpilih</div>
                <small>Siap untuk disetorkan</small>
            </div>
        </div>
        <a href="javascript:void(0)" onclick="processCheckout()" class="btn-checkout">
            Lanjut Setor <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

@endsection

@push('scripts')
    <script>
        // Store selected items map (ID -> Object)
        let selectedItems = new Map();

        document.addEventListener('DOMContentLoaded', () => {
            // 1. Reset selection on load (Fresh start strategy)
            localStorage.removeItem('sampah_checkout_items');

            // 2. Filter Logic
            const tabs = document.querySelectorAll('.filter-btn');
            const cards = document.querySelectorAll('.product-card');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    const filterVal = tab.dataset.filter;
                    cards.forEach(card => {
                        card.style.display = (filterVal === '__all__' || card.dataset
                            .group === filterVal) ? 'flex' : 'none';
                    });
                });
            });
        });

        // Toggle Selection Logic
        function toggleSelection(card, itemData) {
            // Toggle class UI
            const isSelected = card.classList.toggle('selected');
            const txtAdd = card.querySelector('.txt-add');
            const txtAdded = card.querySelector('.txt-added');

            if (isSelected) {
                // Add to map
                selectedItems.set(itemData.id, itemData);
                txtAdd.style.display = 'none';
                txtAdded.style.display = 'inline';
            } else {
                // Remove from map
                selectedItems.delete(itemData.id);
                txtAdd.style.display = 'inline';
                txtAdded.style.display = 'none';
            }

            updateFloatingBar();
        }

        function updateFloatingBar() {
            const bar = document.getElementById('cartBar');
            const countEl = document.getElementById('cartCount');
            const total = selectedItems.size;

            countEl.innerText = total;

            if (total > 0) {
                bar.classList.add('active');
            } else {
                bar.classList.remove('active');
            }
        }

        function processCheckout() {
            if (selectedItems.size === 0) return;

            const itemsArray = Array.from(selectedItems.values());
            localStorage.setItem('sampah_checkout_items', JSON.stringify(itemsArray));
            window.location.href = "{{ route('user.setoran.create') }}";
        }
    </script>
@endpush
