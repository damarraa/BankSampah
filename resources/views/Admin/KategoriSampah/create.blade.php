@extends('layouts.admin') {{-- sesuaikan dengan nama master layout kamu --}}

@section('title', 'Tambah Sampah')

@push('styles')
<style>
    :root{
        --bg:#0b1220; --muted:#93a4c7; --text:#eaf0ff;
        --line:rgba(255,255,255,.10); --shadow:0 18px 60px rgba(0,0,0,.35);
        --radius:16px; --brand:#22c55e;
    }

    .page-bg{
        padding: 20px;
        border-radius: 18px;
        background:
            radial-gradient(1200px 600px at 20% 0%, rgba(34,197,94,.25), transparent 55%),
            radial-gradient(900px 500px at 90% 15%, rgba(59,130,246,.18), transparent 60%),
            var(--bg);
        color: var(--text);
        border: 1px solid rgba(255,255,255,.06);
        max-width: 980px;
        margin: 0 auto;
    }

    .wrap{max-width:980px;margin:0 auto}
    .topbar{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:18px;flex-wrap:wrap}
    .title h2{margin:0;font-size:22px}
    .title p{margin:6px 0 0;color:var(--muted);font-size:13px}

    .card{
        background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
        border:1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding:18px;
    }

    .grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    @media (max-width: 860px){ .grid{grid-template-columns:1fr} }

    label{display:block;font-size:13px;color:var(--muted);margin-bottom:6px}
    input[type="text"], input[type="number"], textarea, select{
        width:100%;
        background: rgba(10,15,26,.55);
        border:1px solid var(--line);
        color:var(--text);
        padding:10px 12px;
        border-radius:12px;
        outline:none;
    }
    input:focus, textarea:focus, select:focus{
        border-color: rgba(34,197,94,.5);
        box-shadow: 0 0 0 3px rgba(34,197,94,.15);
    }
    textarea{min-height:110px;resize:vertical}

    .row{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:14px}

    .btn{
        padding:10px 14px;border-radius:12px;border:1px solid var(--line);
        background: rgba(255,255,255,.04);color:var(--text);
        text-decoration:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;
        font-size:13px;
    }
    .btn-primary{border-color: rgba(34,197,94,.45); background: rgba(34,197,94,.14)}

    .err{
        background: rgba(239,68,68,.12);
        border:1px solid rgba(239,68,68,.35);
        padding:12px 14px;border-radius:14px;margin-bottom:14px
    }
    .err ul{margin:0;padding-left:18px}

    .hint{font-size:12px;color:var(--muted);margin-top:6px}

    .preview{display:flex;align-items:flex-start;gap:12px;margin-top:8px;flex-wrap:wrap}
    .thumb{
        width:96px;height:96px;border-radius:14px;border:1px dashed rgba(255,255,255,.18);
        background: rgba(255,255,255,.03);display:flex;align-items:center;justify-content:center;overflow:hidden;
    }
    .thumb img{width:100%;height:100%;object-fit:cover;display:none}
    .thumb span{color:var(--muted);font-size:12px}

    .file{
        padding:10px 12px;border-radius:12px;border:1px solid var(--line);
        background: rgba(10,15,26,.55);color:var(--text);
        width: 100%;
        max-width: 520px;
    }

    .req{color:rgba(34,197,94,.9)}
</style>
@endpush

@section('content')
<div class="page-bg">
    <div class="wrap">

        <div class="topbar">
            <div class="title">
                <h2>Tambah Data Sampah</h2>
                <p>Isi data dan (opsional) upload gambar sampah.</p>
            </div>
            <a class="btn" href="{{ route('kategori_sampah.index') }}">← Kembali</a>
        </div>

        @if ($errors->any())
            <div class="err">
                <strong style="display:block;margin-bottom:6px;">Terjadi kesalahan:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <form method="POST" action="{{ route('kategori_sampah.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="grid">
                    <div>
                        <label>Nama Sampah <span class="req">*</span></label>
                        <input type="text" name="nama_sampah" value="{{ old('nama_sampah') }}" required>
                    </div>

                    <div>
                        <label>Kategori Sampah <span class="req">*</span></label>
                        <select name="master_kategori_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoriMaster as $k)
                                <option value="{{ $k->id }}" {{ old('master_kategori_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label>Harga Satuan (Rp)</label>
                        <input type="number" name="harga_satuan" value="{{ old('harga_satuan') }}" min="0" step="0.01">
                    </div>

                    <div>
                        <label>Jenis Satuan</label>
                        <input type="text" name="jenis_satuan" value="{{ old('jenis_satuan') }}" placeholder="kg / pcs / botol ...">
                    </div>

                    <div style="grid-column:1/-1;">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" rows="4" placeholder="Keterangan tambahan...">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div style="grid-column:1/-1;">
                        <label>Gambar Sampah (opsional)</label>
                        <input class="file" type="file" name="gambar_sampah" id="gambar_sampah" accept="image/*">

                        <div class="preview">
                            <div class="thumb" id="thumb">
                                <span id="thumbText">Preview</span>
                                <img id="thumbImg" alt="Preview gambar">
                            </div>
                            <div class="hint">
                                Format: JPG/PNG/WEBP. Maks 2MB.<br>
                                Disimpan ke <code>storage/app/public/kategori_sampah</code>.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <button class="btn btn-primary" type="submit">💾 Simpan</button>
                    <a class="btn" href="{{ route('kategori_sampah.index') }}">Batal</a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const input = document.getElementById('gambar_sampah');
    const img = document.getElementById('thumbImg');
    const text = document.getElementById('thumbText');

    input?.addEventListener('change', (e) => {
        const file = e.target.files?.[0];
        if(!file){
            img.style.display = 'none';
            text.style.display = 'block';
            return;
        }
        const url = URL.createObjectURL(file);
        img.src = url;
        img.style.display = 'block';
        text.style.display = 'none';
    });
</script>
@endpush
