@extends('layouts.admin')

@section('title', 'Produksi Karya / Upcycling')

@push('styles')
    <style>
        /* Reusing styles from Penjualan */
        .card-box {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }

        .custom-table th {
            background: #f9fafb;
            padding: 12px;
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .custom-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            color: #1f2937;
            font-size: 0.9rem;
            vertical-align: top;
        }

        /* Modal & Form */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            backdrop-filter: blur(2px);
        }

        .modal-content {
            background: white;
            width: 700px;
            max-width: 95%;
            margin: 3% auto;
            border-radius: 16px;
            height: 85vh;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
            background: #f8fafc;
            text-align: right;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        /* Dynamic Row Style */
        .ingredient-row {
            display: grid;
            grid-template-columns: 3fr 1fr 40px;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }

        .btn-add-row {
            background: #eff6ff;
            color: #3b82f6;
            border: 1px dashed #3b82f6;
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            margin-top: 10px;
        }

        .btn-remove {
            color: #ef4444;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            width: 32px;
            height: 32px;
            cursor: pointer;
            display: grid;
            place-items: center;
        }

        .badge-bahan {
            display: inline-block;
            background: #f3f4f6;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin: 2px;
            border: 1px solid #e5e7eb;
        }
    </style>
@endpush

@section('content')
    <div class="mb-4 d-flex justify-content-between align-items-end">
        <div>
            <h1 style="font-size:1.5rem; font-weight:800; color:#111827;">Produksi Karya (Upcycling)</h1>
            <p style="color:#6b7280;">Kelola pembuatan produk dari bahan baku sampah.</p>
        </div>
        <button onclick="openModal()"
            style="background:#10b981; color:white; border:none; padding:10px 20px; border-radius:8px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Buat Karya Baru
        </button>
    </div>

    {{-- Alert Error --}}
    @if ($errors->any())
        <div
            style="background:#fef2f2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:16px; border:1px solid #fecaca;">
            <ul style="margin:0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-box" style="padding:0; overflow:hidden;">
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Karya</th>
                        <th>Bahan Baku (Resep)</th>
                        <th>Harga Jual</th>
                        <th>Pembeli</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karya as $k)
                        <tr>
                            <td>{{ $k->tanggal_dibuat->format('d/m/Y') }}</td>
                            <td>
                                <div style="font-weight:700; color:#4b5563;">{{ $k->nama_karya }}</div>
                                <div style="font-size:0.75rem; color:#9ca3af;">{{ Str::limit($k->deskripsi, 30) }}</div>
                            </td>
                            <td style="white-space:normal; max-width:300px;">
                                @foreach ($k->bahanBaku as $bahan)
                                    <span class="badge-bahan">
                                        {{ $bahan->kategori->nama_sampah ?? '?' }} : {{ $bahan->jumlah_pakai + 0 }} kg
                                    </span>
                                @endforeach
                            </td>
                            <td style="font-weight:700; color:#10b981;">Rp {{ number_format($k->harga_jual, 0, ',', '.') }}
                            </td>
                            <td>{{ $k->pembeli ?? '-' }}</td>
                            <td>
                                <form action="{{ route('admin.karya.destroy', $k->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus karya ini? Stok bahan baku akan dikembalikan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        style="background:#fee2e2; color:#dc2626; border:none; padding:6px 10px; border-radius:6px; cursor:pointer;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:40px; color:#9ca3af;">Belum ada data
                                produksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:16px;">{{ $karya->links() }}</div>
    </div>

    {{-- MODAL FORM --}}
    <div id="modalKarya" class="modal">
        <form action="{{ route('admin.karya.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h3 style="margin:0;">Input Produksi Karya</h3>
                <span onclick="closeModal()" style="cursor:pointer; font-size:1.5rem;">&times;</span>
            </div>

            <div class="modal-body">
                {{-- Detail Produk --}}
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:16px;">
                    <div>
                        <label class="form-label">Nama Karya / Produk</label>
                        <input type="text" name="nama_karya" class="form-input" placeholder="Contoh: Tas Daur Ulang"
                            required>
                    </div>
                    <div>
                        <label class="form-label">Tanggal Pembuatan</label>
                        <input type="date" name="tanggal_dibuat" class="form-input" value="{{ date('Y-m-d') }}"
                            required>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:16px;">
                    <div>
                        <label class="form-label">Harga Jual (Total)</label>
                        <input type="number" name="harga_jual" class="form-input" placeholder="Rp" required>
                    </div>
                    <div>
                        <label class="form-label">Pembeli (Opsional)</label>
                        <input type="text" name="pembeli" class="form-input" placeholder="Nama Pembeli / Stok">
                    </div>
                </div>

                <div style="margin-bottom:20px;">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-input" rows="2"></textarea>
                </div>

                {{-- Repeater Bahan Baku --}}
                <div style="background:#f8fafc; padding:16px; border-radius:12px; border:1px solid #e2e8f0;">
                    <label class="form-label" style="color:#4b5563; margin-bottom:12px;">Bahan Baku (Mengurangi Stok
                        Gudang)</label>

                    <div id="ingredients-container">
                        {{-- Row Pertama Wajib Ada --}}
                        <div class="ingredient-row">
                            <select name="items[0][kategori_id]" class="form-input" required>
                                <option value="">-- Pilih Bahan --</option>
                                @foreach ($stokBahan as $s)
                                    <option value="{{ $s->id }}" {{ $s->sisa_stok <= 0 ? 'disabled' : '' }}>
                                        {{ $s->nama_sampah }} (Sisa: {{ $s->sisa_stok }} {{ $s->jenis_satuan }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="number" step="0.01" name="items[0][jumlah]" class="form-input"
                                placeholder="Qty" required>
                            <button type="button" class="btn-remove" onclick="removeRow(this)" disabled>×</button>
                        </div>
                    </div>

                    <button type="button" class="btn-add-row" onclick="addRow()">
                        <i class="fa-solid fa-plus"></i> Tambah Bahan Lain
                    </button>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeModal()"
                    style="background:transparent; border:1px solid #d1d5db; padding:10px 20px; border-radius:8px; margin-right:10px; cursor:pointer;">Batal</button>
                <button type="submit"
                    style="background:#8b5cf6; color:white; border:none; padding:10px 24px; border-radius:8px; font-weight:700; cursor:pointer;">Simpan
                    Produksi</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const modal = document.getElementById('modalKarya');
        const container = document.getElementById('ingredients-container');
        let rowCount = 1;

        function openModal() {
            modal.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        // Dynamic Row Logic
        function addRow() {
            const row = document.createElement('div');
            row.className = 'ingredient-row';
            row.innerHTML = `
            <select name="items[${rowCount}][kategori_id]" class="form-input" required>
                <option value="">-- Pilih Bahan --</option>
                @foreach ($stokBahan as $s)
                    <option value="{{ $s->id }}" {{ $s->sisa_stok <= 0 ? 'disabled' : '' }}>
                        {{ $s->nama_sampah }} (Sisa: {{ $s->sisa_stok }} {{ $s->jenis_satuan }})
                    </option>
                @endforeach
            </select>
            <input type="number" step="0.01" name="items[${rowCount}][jumlah]" class="form-input" placeholder="Qty" required>
            <button type="button" class="btn-remove" onclick="removeRow(this)">×</button>
        `;
            container.appendChild(row);
            rowCount++;
        }

        function removeRow(btn) {
            if (container.children.length > 1) {
                btn.parentElement.remove();
            }
        }

        window.onclick = function(e) {
            if (e.target == modal) closeModal();
        }
    </script>
@endpush
