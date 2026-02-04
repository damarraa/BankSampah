@extends('layouts.admin')

@section('title', 'Barang Keluar (Penjualan)')

@push('styles')
    <style>
        /* Menggunakan style dasar yang sama dengan Stok agar konsisten */
        .card-box {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .stat-label {
            font-size: 0.85rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #111827;
            margin-top: 4px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 1.5rem;
            float: right;
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        .custom-table th {
            background: #f9fafb;
            padding: 14px 20px;
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .custom-table td {
            padding: 14px 20px;
            border-bottom: 1px solid #e5e7eb;
            color: #1f2937;
            font-size: 0.9rem;
        }

        .badge-cat {
            background: #ecfdf5;
            color: #059669;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid #d1fae5;
        }

        /* Modal Styles (Reusable) */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
        }

        .modal-content {
            background: white;
            width: 500px;
            max-width: 95%;
            margin: 5% auto;
            border-radius: 16px;
            overflow: hidden;
            animation: slideDown 0.3s;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0
            }

            to {
                transform: translateY(0);
                opacity: 1
            }
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
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            transition: .2s;
        }

        .form-input:focus {
            border-color: #10b981;
            outline: none;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 16px;
        }

        .btn-submit:hover {
            background: #059669;
        }
    </style>
@endpush

@section('content')
    <div class="mb-4 d-flex justify-content-between align-items-end">
        <div>
            <h1 style="font-size:1.5rem; font-weight:800; color:#111827;">Penjualan / Barang Keluar</h1>
            <p style="color:#6b7280;">Rekap penjualan sampah ke pengepul besar.</p>
        </div>
        <button onclick="document.getElementById('modalJual').style.display='block'"
            style="background:#10b981; color:white; border:none; padding:10px 20px; border-radius:8px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-plus"></i> Catat Penjualan
        </button>
    </div>

    @if (session('error'))
        <div
            style="background:#fef2f2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:16px; border:1px solid #fecaca;">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
        </div>
    @endif
    @if (session('success'))
        <div
            style="background:#ecfdf5; color:#065f46; padding:12px; border-radius:8px; margin-bottom:16px; border:1px solid #a7f3d0;">
            <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- STATS CARDS --}}
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:20px; margin-bottom:24px;">
        <div class="card-box">
            <div class="stat-icon" style="background:#ecfdf5; color:#10b981;"><i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div class="stat-label">Total Omzet</div>
            <div class="stat-value">Rp {{ number_format($penjualan->sum('total_pendapatan'), 0, ',', '.') }}</div>
        </div>
        <div class="card-box">
            <div class="stat-icon" style="background:#eff6ff; color:#3b82f6;"><i class="fa-solid fa-truck-ramp-box"></i>
            </div>
            <div class="stat-label">Total Terjual</div>
            <div class="stat-value">{{ number_format($penjualan->sum('jumlah'), 0) }} <span
                    style="font-size:1rem; font-weight:500; color:#6b7280;">Kg</span></div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card-box" style="padding:0; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb; font-weight:700;">Riwayat Transaksi</div>
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Barang</th>
                        <th>Pembeli (Pengepul)</th>
                        <th>Jumlah</th>
                        <th>Harga Jual/Kg</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualan as $p)
                        <tr>
                            <td>{{ $p->tanggal_penjualan->format('d/m/Y') }}</td>
                            <td>
                                <div style="font-weight:600;">{{ $p->kategori->nama_sampah }}</div>
                                <span class="badge-cat">{{ $p->kategori->masterKategori->nama_kategori ?? '-' }}</span>
                            </td>
                            <td>{{ $p->pembeli }}</td>
                            <td>{{ number_format($p->jumlah, 2) }} kg</td>
                            <td>Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                            <td style="font-weight:700; color:#10b981;">Rp
                                {{ number_format($p->total_pendapatan, 0, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('admin.penjualan.destroy', $p->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus data penjualan ini? Stok akan dikembalikan.')">
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
                            <td colspan="7" style="text-align:center; padding:40px; color:#9ca3af;">Belum ada data
                                penjualan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:16px;">
            {{ $penjualan->links() }}
        </div>
    </div>

    {{-- MODAL FORM --}}
    <div id="modalJual" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0; font-size:1.1rem;">Catat Penjualan Baru</h3>
                <span onclick="document.getElementById('modalJual').style.display='none'"
                    style="cursor:pointer; font-size:1.5rem;">&times;</span>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.penjualan.store') }}" method="POST">
                    @csrf

                    <div style="margin-bottom:16px;">
                        <label class="form-label">Tanggal Penjualan</label>
                        <input type="date" name="tanggal_penjualan" class="form-input" value="{{ date('Y-m-d') }}"
                            required>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label class="form-label">Pilih Barang (Stok Tersedia)</label>
                        <select name="kategori_sampah_id" class="form-input" required id="selectBarang">
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($kategoriList as $k)
                                {{-- Hanya tampilkan jika ada stok --}}
                                <option value="{{ $k->id }}" data-stok="{{ $k->sisa_stok }}"
                                    data-satuan="{{ $k->jenis_satuan }}">
                                    {{ $k->nama_sampah }} — Stok: {{ number_format($k->sisa_stok, 2) }}
                                    {{ $k->jenis_satuan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label class="form-label">Pembeli (Pengepul)</label>
                        <input type="text" name="pembeli" class="form-input" placeholder="Nama PT / Pengepul" required>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:16px;">
                        <div>
                            <label class="form-label">Jumlah Jual</label>
                            <input type="number" step="0.01" name="jumlah" id="inputJumlah" class="form-input"
                                placeholder="0" required>
                            <small style="color:#ef4444; display:none;" id="stokWarning">Melebihi stok!</small>
                        </div>
                        <div>
                            <label class="form-label">Harga Jual / Kg</label>
                            <input type="number" name="harga_jual" id="inputHarga" class="form-input" placeholder="Rp"
                                required>
                        </div>
                    </div>

                    <div style="background:#f9fafb; padding:12px; border-radius:8px; margin-bottom:16px; text-align:right;">
                        <span style="font-size:0.9rem; color:#6b7280;">Total Estimasi:</span><br>
                        <strong style="font-size:1.2rem; color:#10b981;" id="totalLabel">Rp 0</strong>
                    </div>

                    <button type="submit" class="btn-submit">Simpan Transaksi</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const selectBarang = document.getElementById('selectBarang');
        const inputJumlah = document.getElementById('inputJumlah');
        const inputHarga = document.getElementById('inputHarga');
        const totalLabel = document.getElementById('totalLabel');
        const stokWarning = document.getElementById('stokWarning');

        let maxStok = 0;

        // Update Max Stok saat pilih barang
        selectBarang.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            maxStok = parseFloat(opt.getAttribute('data-stok')) || 0;
            inputJumlah.max = maxStok; // Set HTML5 validation
            validateStok();
            calcTotal();
        });

        // Hitung Total Real-time
        function calcTotal() {
            const qty = parseFloat(inputJumlah.value) || 0;
            const price = parseFloat(inputHarga.value) || 0;
            const total = qty * price;
            totalLabel.innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Validasi Visual Stok
        function validateStok() {
            const qty = parseFloat(inputJumlah.value) || 0;
            if (qty > maxStok) {
                stokWarning.style.display = 'block';
                inputJumlah.style.borderColor = '#ef4444';
            } else {
                stokWarning.style.display = 'none';
                inputJumlah.style.borderColor = '#d1d5db';
            }
        }

        inputJumlah.addEventListener('input', function() {
            validateStok();
            calcTotal();
        });

        inputHarga.addEventListener('input', calcTotal);

        // Close modal on outside click
        window.onclick = function(e) {
            if (e.target == document.getElementById('modalJual')) {
                document.getElementById('modalJual').style.display = 'none';
            }
        }
    </script>
@endpush
