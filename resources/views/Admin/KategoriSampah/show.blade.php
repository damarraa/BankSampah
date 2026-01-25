@extends('layouts.admin') {{-- sesuaikan dengan nama master layout kamu --}}

@section('title', 'Detail Sampah')

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
            radial-gradient(1200px 600px at 20% 0%, rgba(34,197,94,.22), transparent 55%),
            radial-gradient(900px 500px at 90% 15%, rgba(59,130,246,.16), transparent 60%),
            var(--bg);
        color: var(--text);
        border: 1px solid rgba(255,255,255,.06);
        max-width: 900px;
        margin: 0 auto;
    }

    h2{margin:0 0 16px;font-size:22px}

    .box{
        border:1px solid var(--line);
        border-radius: var(--radius);
        padding:18px;
        background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
        box-shadow: var(--shadow);
    }

    .row{margin-bottom:10px;font-size:14px}
    .label{color:var(--muted);display:block;font-size:12px;margin-bottom:4px}

    .btn{
        padding:10px 14px;
        border-radius:12px;
        border:1px solid var(--line);
        background: rgba(255,255,255,.04);
        color: var(--text);
        text-decoration:none;
        display:inline-flex;
        align-items:center;
        gap:8px;
        cursor:pointer;
        font-size:13px;
    }
    .btn-primary{
        border-color: rgba(34,197,94,.45);
        background: rgba(34,197,94,.14);
    }
    .actions{margin-top:16px;display:flex;gap:10px;flex-wrap:wrap}
</style>
@endpush

@section('content')
<div class="page-bg">

    <h2>Detail Data Sampah</h2>

    <div class="box">
        <div class="row">
            <span class="label">Nama Sampah</span>
            {{ $item->nama_sampah }}
        </div>

        <div class="row">
            <span class="label">Kategori</span>
            {{ $item->masterKategori?->nama_kategori ?? '-' }}
        </div>

        <div class="row">
            <span class="label">Harga Satuan</span>
            {{ $item->harga_satuan !== null ? 'Rp ' . number_format($item->harga_satuan, 0, ',', '.') : '-' }}
        </div>

        <div class="row">
            <span class="label">Jenis Satuan</span>
            {{ $item->jenis_satuan ?? '-' }}
        </div>

        <div class="row">
            <span class="label">Deskripsi</span>
            {{ $item->deskripsi ?? '-' }}
        </div>
    </div>

    <div class="actions">
        <a class="btn" href="{{ route('kategori_sampah.index') }}">⬅️ Kembali</a>
        <a class="btn btn-primary" href="{{ route('kategori_sampah.edit', $item->id) }}">✏️ Edit</a>
    </div>

</div>
@endsection
