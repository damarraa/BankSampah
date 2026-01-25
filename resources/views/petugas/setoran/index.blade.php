@extends('layouts.petugas') {{-- sesuaikan nama file layout petugas kamu --}}

@section('title', 'Setoran Jemput')

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

    .btn{
        padding:10px 14px;border-radius:12px;border:1px solid var(--line);
        background: rgba(255,255,255,.04);
        color:var(--text);text-decoration:none;cursor:pointer;
        display:inline-flex;align-items:center;gap:8px;font-size:13px;
    }
    .btn-primary{border-color: rgba(34,197,94,.45); background: rgba(34,197,94,.14)}
    .btn:hover{filter: brightness(1.06)}

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
    .nowrap{white-space:nowrap}
    .muted{color:var(--muted)}

    .items-list div{margin-bottom:4px}
    .actions{display:flex;gap:8px;flex-wrap:wrap}

    .footer{
        padding:12px 14px;display:flex;justify-content:space-between;align-items:center;
        flex-wrap:wrap;gap:10px;
    }

    /* Pagination */
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
                <h2>Petugas - Setoran Jemput</h2>
                <p class="sub">Daftar setoran metode jemput yang perlu ditangani.</p>
            </div>
        </div>

        <div class="card">
            <div class="toolbar">
                <div class="muted">
                    Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} data
                </div>
                <div></div>
            </div>

            <div style="overflow:auto; max-height: 72vh;">
                <table>
                    <thead>
                        <tr>
                            <th class="nowrap">#</th>
                            <th>User</th>
                            <th>Alamat</th>
                            <th>Item</th>
                            <th class="nowrap">Total</th>
                            <th class="nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $it)
                            <tr>
                                <td class="num nowrap">
                                    {{ $loop->iteration + ($items->currentPage()-1)*$items->perPage() }}
                                </td>
                                <td class="nowrap">{{ $it->user->name ?? '-' }}</td>
                                <td>{{ $it->alamat ?? '-' }}</td>

                                <td class="items-list">
                                    @forelse($it->items as $d)
                                        <div class="muted">
                                            - {{ $d->kategori->nama_sampah ?? '-' }}: {{ $d->jumlah }} {{ $d->satuan ?? '' }}
                                        </div>
                                    @empty
                                        <div class="muted">-</div>
                                    @endforelse
                                </td>

                                <td class="nowrap">Rp {{ number_format($it->estimasi_total) }}</td>

                                <td class="nowrap">
                                    <div class="actions">
                                        <a class="btn btn-primary" href="{{ route('petugas.setoran.show', $it->id) }}">Detail</a>

                                        @if($it->latitude && $it->longitude)
                                            <a class="btn" target="_blank"
                                               href="https://www.google.com/maps/dir/?api=1&destination={{ $it->latitude }},{{ $it->longitude }}&travelmode=driving">
                                                Navigasi
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="muted" style="padding:28px 14px;text-align:center;">
                                    Tidak ada setoran jemput.
                                </td>
                            </tr>
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
