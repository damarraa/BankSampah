@extends('layouts.admin')

@section('title', 'Tambah Data Sampah')

@push('styles')
<style>
    :root {
        --brand: #10b981;
        --brand-dark: #059669;
        --bg: #f8fafc;
        --card: #ffffff;
        --ink: #0f172a;
        --muted: #64748b;
        --line: #e2e8f0;
        --radius: 16px;
        --danger: #ef4444;
    }

    .form-container { max-width: 900px; margin: 0 auto; padding-bottom: 60px; }

    /* ===== HEADER ===== */
    .page-header { text-align: center; margin-bottom: 30px; }
    .page-title {
        font-size: 1.75rem; font-weight: 800; color: var(--ink); margin: 0 0 8px;
        letter-spacing: -0.5px;
    }
    .page-subtitle { color: var(--muted); font-size: 0.95rem; line-height: 1.5; }

    /* ===== LAYOUT GRID ===== */
    .form-grid {
        display: grid; grid-template-columns: 2fr 1.2fr; gap: 24px;
    }
    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }

    /* ===== CARD STYLE ===== */
    .form-card {
        background: var(--card); border-radius: var(--radius);
        box-shadow: 0 10px 30px rgba(0,0,0,0.04); border: 1px solid var(--line);
        overflow: hidden; height: fit-content;
    }
    .card-body { padding: 30px; }
    .card-header {
        padding: 20px 30px; border-bottom: 1px solid var(--line); background: #fff;
        font-weight: 800; color: var(--ink); font-size: 1rem;
    }

    /* ===== FORM ELEMENTS ===== */
    .form-group { margin-bottom: 20px; }

    label {
        display: block; font-weight: 700; color: var(--ink); margin-bottom: 8px; font-size: 0.9rem;
    }
    label span.req { color: var(--danger); margin-left: 3px; }

    .form-control, .form-select {
        width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--line);
        font-size: 0.95rem; color: var(--ink); font-weight: 600; transition: .2s;
        background: #fcfcfc;
    }
    .form-control:focus, .form-select:focus {
        background: #fff; border-color: var(--brand);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); outline: none;
    }
    .form-control::placeholder { color: #cbd5e1; font-weight: 500; }

    /* Input Group (Rp) */
    .input-group { position: relative; }
    .input-prefix {
        position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
        font-weight: 700; color: var(--muted); font-size: 0.9rem;
    }
    .form-control.has-prefix { padding-left: 45px; }

    /* Upload Area */
    .upload-area {
        border: 2px dashed var(--line); border-radius: 16px; padding: 30px;
        text-align: center; cursor: pointer; transition: .2s; background: #f8fafc;
        position: relative; overflow: hidden;
    }
    .upload-area:hover { border-color: var(--brand); background: #ecfdf5; }
    .upload-area.has-file { border-style: solid; padding: 0; background: #000; }

    .upload-placeholder { pointer-events: none; }
    .upload-icon { font-size: 2.5rem; color: var(--muted); margin-bottom: 10px; }
    .upload-text { font-weight: 700; color: var(--ink); margin-bottom: 4px; }
    .upload-hint { font-size: 0.8rem; color: var(--muted); }

    .file-input {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;
    }
    .preview-img {
        width: 100%; height: 100%; object-fit: cover; display: none; aspect-ratio: 4/3;
    }
    .remove-img {
        position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.6);
        color: #fff; width: 32px; height: 32px; border-radius: 50%; display: none;
        align-items: center; justify-content: center; cursor: pointer; transition: .2s; z-index: 2;
    }
    .remove-img:hover { background: #ef4444; }

    /* Footer Actions */
    .form-actions {
        display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px;
    }
    .btn {
        padding: 12px 24px; border-radius: 12px; font-weight: 700; font-size: 0.9rem;
        cursor: pointer; transition: .2s; text-decoration: none; border: 1px solid transparent;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-secondary { background: #fff; border-color: var(--line); color: var(--muted); }
    .btn-secondary:hover { background: #f1f5f9; color: var(--ink); }

    .btn-primary {
        background: var(--brand); color: #fff; border-color: var(--brand);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }
    .btn-primary:hover { background: var(--brand-dark); transform: translateY(-1px); }

    .invalid-feedback { color: var(--danger); font-size: 0.8rem; font-weight: 700; margin-top: 6px; }

    /* Loading */
    .spinner { width: 18px; height: 18px; border: 2px solid #fff; border-bottom-color: transparent; border-radius: 50%; animation: rot 1s linear infinite; display: none; }
    .btn-loading .spinner { display: inline-block; }
    .btn-loading span { display: none; }
    @keyframes rot { 100% { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div class="form-container">

    <div class="page-header">
        <h1 class="page-title">Tambah Item Sampah</h1>
        <p class="page-subtitle">Lengkapi detail item, harga satuan, dan gambar referensi.</p>
    </div>

    <form action="{{ route('kategori_sampah.store') }}" method="POST" enctype="multipart/form-data" id="mainForm">
        @csrf

        <div class="form-grid">

            {{-- KOLOM KIRI: INFO UTAMA --}}
            <div class="form-card">
                <div class="card-header">Informasi Dasar</div>
                <div class="card-body">
                    {{-- Nama Sampah --}}
                    <div class="form-group">
                        <label>Nama Sampah <span class="req">*</span></label>
                        <input type="text" name="nama_sampah" class="form-control @error('nama_sampah') is-invalid @enderror"
                               value="{{ old('nama_sampah') }}" placeholder="Contoh: Botol Plastik, Kardus Bekas" required>
                        @error('nama_sampah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Kategori Master --}}
                    <div class="form-group">
                        <label>Kategori Induk <span class="req">*</span></label>
                        <select name="master_kategori_id" class="form-select @error('master_kategori_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoriMaster as $km)
                                <option value="{{ $km->id }}" {{ old('master_kategori_id') == $km->id ? 'selected' : '' }}>
                                    {{ $km->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('master_kategori_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="form-group">
                        <label>Deskripsi <span style="font-weight:400; color:var(--muted)">(Opsional)</span></label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Keterangan tambahan...">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: HARGA & GAMBAR --}}
            <div class="right-col">

                {{-- Card Harga --}}
                <div class="form-card" style="margin-bottom: 24px;">
                    <div class="card-header">Penetapan Harga</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Harga Satuan</label>
                            <div class="input-group">
                                <span class="input-prefix">Rp</span>
                                <input type="number" name="harga_satuan" class="form-control has-prefix"
                                       value="{{ old('harga_satuan') }}" placeholder="0">
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label>Satuan (Unit)</label>
                            <input type="text" name="jenis_satuan" class="form-control"
                                   value="{{ old('jenis_satuan', 'kg') }}" placeholder="kg, pcs, liter">
                        </div>
                    </div>
                </div>

                {{-- Card Gambar --}}
                <div class="form-card">
                    <div class="card-header">Gambar Referensi</div>
                    <div class="card-body">
                        <div class="upload-area" id="uploadArea">
                            <input type="file" name="gambar_sampah" id="fileInput" class="file-input" accept="image/*">

                            <div class="upload-placeholder" id="placeholder">
                                <div class="upload-icon"><i class="fa-regular fa-image"></i></div>
                                <div class="upload-text">Upload Gambar</div>
                                <div class="upload-hint">Klik atau drag file ke sini<br>(Max 2MB)</div>
                            </div>

                            <img id="preview" class="preview-img">
                            <div class="remove-img" id="removeBtn" title="Hapus Gambar"><i class="fa-solid fa-times"></i></div>
                        </div>
                        @error('gambar_sampah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="form-actions">
            <a href="{{ route('kategori_sampah.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" id="btnSubmit">
                <div class="spinner"></div> <span>Simpan Data</span>
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    const fileInput = document.getElementById('fileInput');
    const preview = document.getElementById('preview');
    const placeholder = document.getElementById('placeholder');
    const uploadArea = document.getElementById('uploadArea');
    const removeBtn = document.getElementById('removeBtn');
    const form = document.getElementById('mainForm');
    const btnSubmit = document.getElementById('btnSubmit');

    // Image Preview Logic
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
                uploadArea.classList.add('has-file');
                removeBtn.style.display = 'flex';
            }
            reader.readAsDataURL(file);
        }
    });

    // Remove Image Logic
    removeBtn.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent triggering file input
        fileInput.value = '';
        preview.src = '';
        preview.style.display = 'none';
        placeholder.style.display = 'block';
        uploadArea.classList.remove('has-file');
        this.style.display = 'none';
    });

    // Submit Loading
    form.addEventListener('submit', function() {
        if(form.checkValidity()) {
            btnSubmit.classList.add('btn-loading');
            btnSubmit.setAttribute('disabled', true);
        }
    });
</script>
@endpush
