@extends('layouts.admin') {{-- sesuaikan: nama file master layout kamu, contoh resources/views/layouts/admin.blade.php --}}

@section('title', 'Data Sampah')

@push('styles')
<style>
    :root{
        --bg:#0b1220; --muted:#93a4c7; --text:#eaf0ff;
        --line:rgba(255,255,255,.10); --shadow:0 18px 60px rgba(0,0,0,.35);
        --radius:16px; --brand:#22c55e; --danger:#ef4444;
    }

    /* Area konten (di dalam content-wrapper master) */
    .wrap{max-width:1100px;margin:0 auto}
    .topbar{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:16px;flex-wrap:wrap}
    h2{margin:0;font-size:22px}
    .sub{margin:6px 0 0;color:var(--muted);font-size:13px}

    .card{
        background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
        border:1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow:hidden;
    }
    .toolbar{
        display:flex; gap:10px; align-items:center; justify-content:space-between;
        padding:14px;border-bottom:1px solid var(--line);flex-wrap:wrap;
    }
    .left, .right{display:flex; gap:10px; align-items:center; flex-wrap:wrap}

    .btn{
        padding:10px 14px;border-radius:12px;border:1px solid var(--line);
        background: rgba(255,255,255,.04); color:var(--text);
        text-decoration:none; cursor:pointer; display:inline-flex;align-items:center;gap:8px;
        font-size:13px;
    }
    .btn-primary{border-color: rgba(34,197,94,.45); background: rgba(34,197,94,.14)}
    .btn-danger{border-color: rgba(239,68,68,.45); background: rgba(239,68,68,.10); color:#ffd2d2}

    .input{
        padding:10px 12px;border-radius:12px;border:1px solid var(--line);
        background: rgba(10,15,26,.55); color:var(--text); outline:none;
    }

    table{width:100%;border-collapse:separate;border-spacing:0}
    thead th{
        text-align:left;font-size:12px;color:var(--muted);
        padding:12px 14px;border-bottom:1px solid var(--line);
        background: rgba(255,255,255,.03);
        position: sticky; top:0; backdrop-filter: blur(8px);
    }
    tbody td{padding:12px 14px;border-bottom:1px solid rgba(255,255,255,.06);font-size:13px;vertical-align:middle}
    tbody tr:hover td{background: rgba(255,255,255,.02)}
    .num{color:var(--muted);font-variant-numeric: tabular-nums}
    .actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}

    .thumb{
        width:44px;height:44px;border-radius:12px;border:1px solid rgba(255,255,255,.12);
        background: rgba(255,255,255,.03);overflow:hidden;display:flex;align-items:center;justify-content:center;
    }
    .thumb img{width:100%;height:100%;object-fit:cover}

    .alert{
        margin-bottom:12px;padding:12px 14px;border-radius:14px;
        border:1px solid rgba(34,197,94,.35); background: rgba(34,197,94,.12);
        color: var(--text);
    }

    .footer{padding:12px 14px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
    .links{color:var(--muted)}
    form{margin:0}
    .empty{padding:28px 14px;text-align:center;color:var(--muted)}
    .nowrap{white-space:nowrap}

    /* Biar kontras di background master (master bg abu-abu), kita pakai background gelap khusus area konten ini */
    .page-bg{
        padding: 18px;
        border-radius: 18px;
        background:
            radial-gradient(1200px 600px at 20% 0%, rgba(34,197,94,.22), transparent 55%),
            radial-gradient(900px 500px at 90% 15%, rgba(59,130,246,.16), transparent 60%),
            var(--bg);
        color: var(--text);
        border: 1px solid rgba(255,255,255,.06);
    }

    /* Pagination laravel default sering putih/abu — kita styling ringan */
    .pagination { display:flex; gap:8px; flex-wrap:wrap; }
    .pagination .page-link, .pagination a, .pagination span{
        color: var(--text) !important;
        background: rgba(255,255,255,.04) !important;
        border: 1px solid var(--line) !important;
        border-radius: 10px;
        padding: 8px 12px;
        text-decoration: none;
    }
    .pagination .active span{
        border-color: rgba(34,197,94,.45) !important;
        background: rgba(34,197,94,.14) !important;
    }
</style>
@endpush

@section('content')
<div class="page-bg">
    <div class="wrap">

        <div class="topbar">
            <div>
                <h2>Data Sampah</h2>
                <p class="sub">Kelola kategori sampah + gambar.</p>
            </div>
            <a class="btn" href="{{ route('master_kategori_sampah.index') }}">📚 Master Kategori</a>
        </div>

        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="toolbar">
                <div class="left">
                    <a class="btn btn-primary" href="{{ route('kategori_sampah.create') }}">➕ Tambah</a>
                </div>

                <div class="right">
                    <form method="GET" action="{{ route('kategori_sampah.index') }}" class="right">
                        <input class="input" type="text" name="q" value="{{ $q }}" placeholder="Cari nama/kategori...">
                        <button class="btn" type="submit">🔎 Cari</button>
                        @if($q)
                            <a class="btn" href="{{ route('kategori_sampah.index') }}">↺ Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <div style="overflow:auto; max-height: 72vh;">
                <table>
                    <thead>
                        <tr>
                            <th class="nowrap">#</th>
                            <th>Gambar</th>
                            <th>Nama Sampah</th>
                            <th>Kategori</th>
                            <th class="nowrap">Harga Satuan</th>
                            <th class="nowrap">Jenis Satuan</th>
                            <th class="nowrap">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="num nowrap">{{ $loop->iteration + ($items->currentPage()-1)*$items->perPage() }}</td>

                            <td class="nowrap">
                                <div class="thumb">
                                    @if($item->gambar_sampah)
                                        <img src="{{ asset('storage/'.$item->gambar_sampah) }}" alt="Gambar">
                                    @else
                                        <span class="num">—</span>
                                    @endif
                                </div>
                            </td>

                            <td>{{ $item->nama_sampah }}</td>

                            {{-- relasi masterKategori --}}
                            <td>{{ $item->masterKategori?->nama_kategori ?? '-' }}</td>

                            <td class="nowrap">
                                {{ $item->harga_satuan !== null ? 'Rp ' . number_format($item->harga_satuan, 0, ',', '.') : '-' }}
                            </td>

                            <td class="nowrap">{{ $item->jenis_satuan ?? '-' }}</td>

                            <td class="nowrap">
                                <div class="actions">
                                    <a class="btn" href="{{ route('kategori_sampah.edit', $item->id) }}">✏️ Edit</a>

                                    <form method="POST"
                                          action="{{ route('kategori_sampah.destroy', $item->id) }}"
                                          onsubmit="return confirm('Yakin hapus data ini? (gambar juga akan terhapus)')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">🗑️ Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty">Belum ada data.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="footer">
                <div class="links">
                    Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} data
                </div>
                <div>{{ $items->links() }}</div>
            </div>
        </div>

    </div>
</div>
@endsection
