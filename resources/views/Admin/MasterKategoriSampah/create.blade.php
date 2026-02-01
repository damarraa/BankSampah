@extends('layouts.admin')

@section('title', 'Tambah Master Kategori')

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

        .form-container {
            max-width: 700px;
            /* Lebar ideal untuk form agar fokus */
            margin: 0 auto;
            padding-bottom: 60px;
        }

        /* ===== HEADER ===== */
        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--ink);
            margin: 0 0 8px;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* ===== CARD STYLE ===== */
        .form-card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--line);
            overflow: hidden;
        }

        .card-body {
            padding: 30px;
        }

        /* ===== FORM ELEMENTS ===== */
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        label {
            display: block;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        label span.req {
            color: var(--danger);
            margin-left: 3px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid var(--line);
            font-size: 0.95rem;
            color: var(--ink);
            font-weight: 600;
            transition: .2s;
            background: #fcfcfc;
        }

        .form-control:focus {
            background: #fff;
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #cbd5e1;
            font-weight: 500;
        }

        .form-control.is-invalid {
            border-color: var(--danger);
            background: #fef2f2;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
            line-height: 1.6;
        }

        /* Helper & Counter */
        .input-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 6px;
            font-size: 0.75rem;
        }

        .form-text {
            color: var(--muted);
        }

        .char-count {
            color: var(--muted);
            font-weight: 600;
            transition: .2s;
        }

        .char-count.limit {
            color: var(--danger);
        }

        /* Error Message */
        .invalid-feedback {
            color: var(--danger);
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ===== ACTIONS FOOTER ===== */
        .card-footer {
            background: #f8fafc;
            border-top: 1px solid var(--line);
            padding: 20px 30px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn {
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: .2s;
            text-decoration: none;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary {
            background: #fff;
            border-color: var(--line);
            color: var(--muted);
        }

        .btn-secondary:hover {
            background: #f1f5f9;
            color: var(--ink);
        }

        .btn-primary {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .btn-primary:hover {
            background: var(--brand-dark);
            transform: translateY(-1px);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Loading Spinner */
        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #fff;
            border-bottom-color: transparent;
            border-radius: 50%;
            display: none;
            animation: rotation 1s linear infinite;
        }

        .btn-loading .spinner {
            display: inline-block;
        }

        .btn-loading span {
            display: none;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@section('content')
    <div class="form-container">

        {{-- Header --}}
        <div class="page-header">
            <h1 class="page-title">Tambah Kategori Baru</h1>
            <p class="page-subtitle">Buat kategori master sampah (misal: Plastik, Kertas) untuk pengelompokan jenis sampah.
            </p>
        </div>

        {{-- Alert Error Global --}}
        @if ($errors->any())
            <div
                style="background:#fef2f2; border:1px solid #fee2e2; border-radius:12px; padding:16px; margin-bottom:24px; display:flex; gap:12px;">
                <i class="fa-solid fa-circle-exclamation" style="color:#ef4444; margin-top:2px;"></i>
                <div>
                    <div style="font-weight:700; color:#991b1b; margin-bottom:4px;">Gagal menyimpan data</div>
                    <ul style="margin:0; padding-left:16px; color:#b91c1c; font-size:0.9rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('master_kategori_sampah.store') }}" method="POST" id="mainForm">
            @csrf
            <div class="form-card">
                <div class="card-body">

                    {{-- Nama Kategori --}}
                    <div class="form-group">
                        <label for="nama_kategori">Nama Kategori <span class="req">*</span></label>
                        <input type="text" name="nama_kategori" id="nama_kategori"
                            class="form-control @error('nama_kategori') is-invalid @enderror"
                            value="{{ old('nama_kategori') }}" placeholder="Contoh: Plastik, Logam, Elektronik"
                            maxlength="50" autofocus required>

                        <div class="input-footer">
                            <span class="form-text">Gunakan nama yang singkat dan jelas.</span>
                            <span class="char-count" id="countName">0/50</span>
                        </div>

                        @error('nama_kategori')
                            <div class="invalid-feedback"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="deskripsi">Deskripsi <span
                                style="font-weight:400; color:var(--muted);">(Opsional)</span></label>
                        <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror"
                            placeholder="Jelaskan jenis sampah apa saja yang masuk kategori ini..." maxlength="255">{{ old('deskripsi') }}</textarea>

                        <div class="input-footer">
                            <span class="form-text">Maksimal 255 karakter.</span>
                            <span class="char-count" id="countDesc">0/255</span>
                        </div>

                        @error('deskripsi')
                            <div class="invalid-feedback"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                <div class="card-footer">
                    <a href="{{ route('master_kategori_sampah.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <div class="spinner"></div>
                        <span><i class="fa-solid fa-save"></i> Simpan Data</span>
                    </button>
                </div>
            </div>
        </form>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const nameInput = document.getElementById('nama_kategori');
            const descInput = document.getElementById('deskripsi');
            const countName = document.getElementById('countName');
            const countDesc = document.getElementById('countDesc');
            const form = document.getElementById('mainForm');
            const btnSubmit = document.getElementById('btnSubmit');

            // Character Counter Logic
            function updateCount(input, counter, max) {
                const len = input.value.length;
                counter.innerText = `${len}/${max}`;
                if (len >= max) counter.classList.add('limit');
                else counter.classList.remove('limit');
            }

            // Init Counters
            updateCount(nameInput, countName, 50);
            updateCount(descInput, countDesc, 255);

            // Event Listeners
            nameInput.addEventListener('input', () => updateCount(nameInput, countName, 50));
            descInput.addEventListener('input', () => updateCount(descInput, countDesc, 255));

            // Submit Loading State
            form.addEventListener('submit', function() {
                if (form.checkValidity()) {
                    btnSubmit.classList.add('btn-loading');
                    btnSubmit.setAttribute('disabled', true);
                }
            });
        });
    </script>
@endpush
