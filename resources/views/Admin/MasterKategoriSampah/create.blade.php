@extends('layouts.admin') {{-- sesuaikan dengan nama master layout kamu --}}

@section('title', 'Tambah Master Kategori')

@push('styles')
<style>
    :root{
        --bg:#0b1220; --text:#eaf0ff; --muted:#93a4c7;
        --line:rgba(255,255,255,.10);
        --shadow:0 18px 60px rgba(0,0,0,.35);
        --radius:16px;
        --brand:#22c55e; --danger:#ef4444;
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

    label{display:block;font-size:13px;color:var(--muted);margin-bottom:6px}
    input[type="text"], textarea{
        width:100%;
        background: rgba(10,15,26,.55);
        border:1px solid var(--line);
        color:var(--text);
        padding:10px 12px;
        border-radius:12px;
        outline:none;
    }
    input:focus, textarea:focus{
        border-color: rgba(34,197,94,.5);
        box-shadow: 0 0 0 3px rgba(34,197,94,.15);
    }
    textarea{min-height:110px;resize:vertical}

    .row{margin-bottom:12px}

    .btn{
        padding:10px 14px;border-radius:12px;border:1px solid var(--line);
        background: rgba(255,255,255,.04);
        color:var(--text);text-decoration:none;cursor:pointer;
        display:inline-flex;align-items:center;gap:8px;font-size:13px;
    }
    .btn-primary{border-color: rgba(34,197,94,.45); background: rgba(34,197,94,.14)}
    .btn:hover{filter: brightness(1.06)}

    .err{
        background: rgba(239,68,68,.12);
        border:1px solid rgba(239,68,68,.35);
        padding:12px 14px;border-radius:14px;margin-bottom:14px
    }
    .err ul{margin:0;padding-left:18px}

    .req{color:rgba(34,197,94,.9)}
</style>
@endpush

@section('content')
<div class="page-bg">
    <div class="wrap">
        <div class="topbar">
            <div class="title">
                <h2>Tambah Master Kategori</h2>
                <p>Tambahkan kategori untuk dipakai di data sampah.</p>
            </div>
            <a class="btn" href="{{ route('master_kategori_sampah.index') }}">← Kembali</a>
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
            <form method="POST" action="{{ route('master_kategori_sampah.store') }}">
                @csrf

                <div class="row">
                    <label>Nama Kategori <span class="req">*</span></label>
                    <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}" required placeholder="Contoh: Plastik">
                </div>

                <div class="row">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" placeholder="Keterangan tambahan...">{{ old('deskripsi') }}</textarea>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px">
                    <button class="btn btn-primary" type="submit">💾 Simpan</button>
                    <a class="btn" href="{{ route('master_kategori_sampah.index') }}">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
