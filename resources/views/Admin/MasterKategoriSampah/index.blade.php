@extends('layouts.admin') {{-- sesuaikan nama master layout kamu --}}

@section('title', 'Master Kategori Sampah')

@push('styles')
<style>
    :root{
        --bg:#0b1220; --text:#eaf0ff; --muted:#93a4c7;
        --line:rgba(255,255,255,.10);
        --card:rgba(255,255,255,.05);
        --brand:#22c55e; --danger:#ef4444;
        --shadow:0 18px 60px rgba(0,0,0,.35);
        --radius:16px;
    }

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
        display:flex;gap:10px;align-items:center;justify-content:space-between;
        padding:14px;border-bottom:1px solid var(--line);flex-wrap:wrap;
    }

    .left,.right{display:flex;gap:10px;align-items:center;flex-wrap:wrap}

    .btn{
        padding:10px 14px;border-radius:12px;border:1px solid var(--line);
        background: rgba(255,255,255,.04);
        color:var(--text);text-decoration:none;cursor:pointer;
        display:inline-flex;align-items:center;gap:8px;font-size:13px;
    }
    .btn-primary{border-color: rgba(34,197,94,.45); background: rgba(34,197,94,.14)}
    .btn-danger{border-color: rgba(239,68,68,.45); background: rgba(239,68,68,.10); color:#ffd2d2}
    .btn:hover{filter: brightness(1.06)}

    .input{
        padding:10px 12px;border-radius:12px;border:1px solid var(--line);
        background: rgba(10,15,26,.55); color:var(--text);
        outline:none;
    }
    .input:focus{border-color: rgba(34,197,94,.5); box-shadow: 0 0 0 3px rgba(34,197,94,.15);}

    table{width:100%;border-collapse:separate;border-spacing:0}
    thead th{
        text-align:left;font-size:12px;color:var(--muted);
        padding:12px 14px;border-bottom:1px solid var(--line);
        background: rgba(255,255,255,.03);
        position: sticky; top:0;
        backdrop-filter: blur(8px);
    }
    tbody td{
        padding:12px 14px;border-bottom:1px solid rgba(255,255,255,.06);
        vertical-align:top;font-size:13px;
    }
    tbody tr:hover td{background: rgba(255,255,255,.02)}

    .num{color:var(--muted);font-variant-numeric: tabular-nums}
    .actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}

    .alert{
        margin-bottom:12px;padding:12px 14px;border-radius:14px;
        border:1px solid rgba(34,197,94,.35);
        background: rgba(34,197,94,.12);
    }

    .pill{
        display:inline-flex;align-items:center;
        padding:6px 10px;border-radius:999px;
        border:1px solid rgba(255,255,255,.10);
        background: rgba(255,255,255,.03);
        color: var(--muted);font-size:12px;
    }

    .empty{padding:28px 14px;text-align:center;color:var(--muted)}

    .footer{
        padding:12px 14px;display:flex;justify-content:space-between;align-items:center;
        flex-wrap:wrap;gap:10px;
    }

    form{margin:0}
    .nowrap{white-space:nowrap}

    /* Pagination laravel default */
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
                <h2>Master Kategori Sampah</h2>
                <p class="sub">Kelola kategori master (Plastik/Kertas/Logam/dll) untuk dipilih saat membuat data sampah.</p>
            </div>
            <a class="btn" href="{{ route('kategori_sampah.index') }}">← Kembali ke Data Sampah</a>
        </div>

        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="toolbar">
                <div class="left">
                    <a class="btn btn-primary" href="{{ route('master_kategori_sampah.create') }}">➕ Tambah Kategori</a>
                    @if($q)
                        <span class="pill">Filter: "{{ $q }}"</span>
                    @endif
                </div>

                <div class="right">
                    <form method="GET" action="{{ route('master_kategori_sampah.index') }}" class="right">
                        <input class="input" type="text" name="q" value="{{ $q }}" placeholder="Cari kategori...">
                        <button class="btn" type="submit">🔎 Cari</button>
                        @if($q)
                            <a class="btn" href="{{ route('master_kategori_sampah.index') }}">↺ Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <div style="overflow:auto; max-height: 72vh;">
                <table>
                    <thead>
                        <tr>
                            <th class="nowrap">#</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th class="nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="num nowrap">{{ $loop->iteration + ($items->currentPage()-1)*$items->perPage() }}</td>
                            <td class="nowrap">{{ $item->nama_kategori }}</td>
                            <td>{{ $item->deskripsi ?? '-' }}</td>
                            <td class="nowrap">
                                <div class="actions">
                                    <a class="btn" href="{{ route('master_kategori_sampah.edit', $item->id) }}">✏️ Edit</a>

                                    <form method="POST" action="{{ route('master_kategori_sampah.destroy', $item->id) }}"
                                          onsubmit="return confirm('Yakin hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">🗑️ Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty">Belum ada data kategori.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="footer">
                <div class="num">
                    Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} data
                </div>
                <div>{{ $items->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
