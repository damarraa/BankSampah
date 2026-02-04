@extends('layouts.admin')

@section('title', 'Monitoring Stok Gudang')

@push('styles')
    <style>
        /* Custom style untuk halaman ini */
        .stok-card {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--line);
            box-shadow: var(--shadow-sm);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stok-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1.5rem;
        }

        .icon-weight {
            background: #eff6ff;
            color: #3b82f6;
        }

        .icon-money {
            background: #ecfdf5;
            color: #10b981;
        }

        .stok-info h4 {
            font-size: 0.8rem;
            color: var(--muted);
            text-transform: uppercase;
            margin: 0;
            font-weight: 700;
        }

        .stok-info .val {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--ink);
            line-height: 1.2;
        }

        /* Progress bar dalam tabel */
        .progress-bg {
            background: #f1f5f9;
            height: 6px;
            border-radius: 10px;
            width: 100px;
            overflow: hidden;
            margin-top: 6px;
        }

        .progress-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 10px;
        }

        /* Search Input */
        .search-wrapper {
            position: relative;
            width: 300px;
            max-width: 100%;
        }

        .search-input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border: 1px solid var(--line);
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
        }

        /* MODAL STYLES */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 0;
            border: 1px solid #888;
            width: 500px;
            max-width: 90%;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            animation: modalSlide 0.3s;
        }

        @keyframes modalSlide {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f9fafb;
            border-radius: 16px 16px 0 0;
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--ink);
            margin: 0;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
            transition: 0.2s;
        }

        .close:hover {
            color: #ef4444;
        }

        .modal-body {
            padding: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--ink);
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
    </style>
@endpush

@section('content')
    {{-- Success Message --}}
    @if (session('success'))
        <div
            style="background:#d1fae5; color:#065f46; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-weight:600; border:1px solid #a7f3d0;">
            <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- HEADER AREA --}}
    <div class="mb-4 d-flex justify-content-between align-items-end flex-wrap gap-3">
        <div>
            <h1 style="font-size:1.5rem; font-weight:800; color:var(--ink); margin-bottom:4px;">Stok Gudang</h1>
            <p style="color:var(--muted); margin:0;">Monitoring stok real-time dan nilai aset sampah.</p>
        </div>

        {{-- BUTTON TRIGGER MODAL --}}
        <button id="btnTambahStok" class="btn"
            style="background:var(--primary); border:none; padding:12px 20px; border-radius:8px; font-weight:600; color:#fff; display:inline-flex; align-items:center; gap:8px; cursor:pointer; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); transition: 0.2s;">
            <i class="fa-solid fa-plus-circle"></i> Input Stok Manual
        </button>
    </div>

    {{-- SUMMARY CARDS --}}
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:20px; margin-bottom:24px;">
        <div class="stok-card">
            <div class="stok-icon icon-weight"><i class="fa-solid fa-weight-hanging"></i></div>
            <div class="stok-info">
                <h4>Total Berat</h4>
                <div class="val">{{ number_format($stok->sum('total_berat'), 2) }} <span
                        style="font-size:1rem; font-weight:500">kg</span></div>
            </div>
        </div>
        <div class="stok-card">
            <div class="stok-icon icon-money"><i class="fa-solid fa-vault"></i></div>
            <div class="stok-info">
                <h4>Estimasi Nilai Aset</h4>
                <div class="val">Rp {{ number_format($stok->sum('total_nilai'), 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="card"
        style="background:#fff; border-radius:var(--radius); border:1px solid var(--line); overflow:hidden; box-shadow:var(--shadow-sm);">

        {{-- HEADER WITH SEARCH --}}
        <div
            style="padding:16px 20px; border-bottom:1px solid var(--line); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="font-weight:700; color:var(--ink); font-size:1.1rem; display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-list"></i> Rincian Stok
            </div>

            <div class="search-wrapper">
                <i class="fa-solid fa-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Cari jenis sampah..."
                    onkeyup="filterTable()">
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;" id="stokTable">
                <thead style="background:#f9fafb; border-bottom:1px solid var(--line);">
                    <tr>
                        <th
                            style="padding:12px 20px; text-align:left; font-size:0.75rem; color:var(--muted); text-transform:uppercase;">
                            Jenis Sampah</th>
                        <th
                            style="padding:12px 20px; text-align:left; font-size:0.75rem; color:var(--muted); text-transform:uppercase;">
                            Kategori</th>
                        <th
                            style="padding:12px 20px; text-align:left; font-size:0.75rem; color:var(--muted); text-transform:uppercase;">
                            Total Berat</th>
                        <th
                            style="padding:12px 20px; text-align:left; font-size:0.75rem; color:var(--muted); text-transform:uppercase;">
                            Total Nilai</th>
                        <th
                            style="padding:12px 20px; text-align:right; font-size:0.75rem; color:var(--muted); text-transform:uppercase;">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $maxBerat = $stok->max('total_berat') > 0 ? $stok->max('total_berat') : 1; @endphp
                    @forelse($stok as $item)
                        <tr style="border-bottom:1px solid var(--line); transition:background .2s;" class="stok-row">
                            <td style="padding:14px 20px; color:var(--ink); font-weight:600;">
                                {{ $item->nama_sampah }}
                            </td>
                            <td style="padding:14px 20px;">
                                <span
                                    style="background:#ecfdf5; color:#059669; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:700; border:1px solid #d1fae5;">
                                    {{ $item->masterKategori->nama_kategori ?? '-' }}
                                </span>
                            </td>
                            <td style="padding:14px 20px;">
                                <div style="font-weight:700; color:var(--ink);">{{ number_format($item->total_berat, 2) }}
                                    {{ $item->jenis_satuan }}</div>
                                <div class="progress-bg">
                                    <div class="progress-fill" style="width: {{ ($item->total_berat / $maxBerat) * 100 }}%">
                                    </div>
                                </div>
                            </td>
                            <td style="padding:14px 20px; font-family:monospace; font-weight:600; color:var(--ink);">
                                Rp {{ number_format($item->total_nilai, 0, ',', '.') }}
                            </td>
                            <td style="padding:14px 20px; text-align:right;">
                                <a href="{{ route('kategori_sampah.edit', $item->id) }}"
                                    style="display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border:1px solid var(--line); border-radius:8px; text-decoration:none; color:var(--ink); font-size:0.8rem; font-weight:600; transition:.2s;"
                                    onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)'"
                                    onmouseout="this.style.borderColor='var(--line)'; this.style.color='var(--ink)'">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:40px; text-align:center; color:var(--muted);">
                                <i class="fa-solid fa-box-open"
                                    style="font-size:2rem; margin-bottom:10px; opacity:0.5;"></i>
                                <p>Belum ada data stok masuk.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Empty State untuk Search --}}
            <div id="noResult" style="display:none; padding:40px; text-align:center; color:var(--muted);">
                <i class="fa-solid fa-search" style="font-size:2rem; margin-bottom:10px; opacity:0.5;"></i>
                <p>Tidak ditemukan data yang cocok.</p>
            </div>
        </div>
    </div>

    {{-- MODAL INPUT STOK --}}
    <div id="modalStok" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-box-archive me-2" style="color:var(--primary)"></i> Input Stok
                    Manual</h3>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.stok.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Jenis Sampah</label>
                        <select name="kategori_sampah_id" class="form-control" required id="selectKategori"
                            style="cursor: pointer;">
                            <option value="">-- Pilih Jenis Sampah --</option>
                            @foreach ($kategoriList as $k)
                                <option value="{{ $k->id }}" data-harga="{{ $k->harga_satuan }}"
                                    data-satuan="{{ $k->jenis_satuan }}">
                                    {{ $k->nama_sampah }} ({{ $k->masterKategori->nama_kategori ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                        <div>
                            <label>Berat / Jumlah</label>
                            <div style="position:relative;">
                                <input type="number" step="0.01" name="berat" id="inputBerat" class="form-control"
                                    required placeholder="0">
                                <span id="labelSatuan"
                                    style="position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:0.8rem; color:#999; font-weight:600; background:#fff; padding-left:4px;">Kg</span>
                            </div>
                        </div>
                        <div>
                            <label>Nilai Aset (Rp)</label>
                            <input type="number" name="harga_total" id="inputHarga" class="form-control" required
                                placeholder="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Catatan (Sumber/Keterangan)</label>
                        <textarea name="catatan" class="form-control" rows="2"
                            placeholder="Contoh: Stok opname bulan Maret, atau beli putus dari pengepul X"></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-save me-2"></i> Simpan Stok
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Search Filter Logic
        function filterTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll(".stok-row");
            const noResult = document.getElementById("noResult");
            let hasVisible = false;

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = "";
                    hasVisible = true;
                } else {
                    row.style.display = "none";
                }
            });

            noResult.style.display = hasVisible ? "none" : "block";
        }

        // Modal Logic
        const modal = document.getElementById("modalStok");
        const btn = document.getElementById("btnTambahStok");
        const span = document.getElementsByClassName("close")[0];

        // Open Modal
        btn.onclick = function() {
            modal.style.display = "block";
            // Reset form slightly if needed, or focus on first input
            document.getElementById("selectKategori").focus();
        }

        // Close Modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Close on click outside
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Auto Calculate Price Logic
        const selectKat = document.getElementById('selectKategori');
        const inputBerat = document.getElementById('inputBerat');
        const inputHarga = document.getElementById('inputHarga');
        const labelSatuan = document.getElementById('labelSatuan');

        function calculate() {
            const berat = parseFloat(inputBerat.value) || 0;
            const opt = selectKat.options[selectKat.selectedIndex];
            // Jika belum pilih, harga satuan 0
            const hargaSatuan = opt.value ? (parseFloat(opt.getAttribute('data-harga')) || 0) : 0;

            // Auto fill price suggestion (Rounding to avoid decimals in RP)
            if (berat > 0) {
                inputHarga.value = Math.round(berat * hargaSatuan);
            }
        }

        selectKat.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            // Update satuan label (Kg/Pcs/Ltr)
            labelSatuan.innerText = opt.getAttribute('data-satuan') || 'Kg';
            calculate();
        });

        inputBerat.addEventListener('input', calculate);
    </script>
@endpush
