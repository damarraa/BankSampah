@extends('layouts.petugas')

@section('title', 'Daftar Tugas')

@push('styles')
<style>
    /* Hero Header */
    .page-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        padding: 30px 0 60px; color: #fff; border-radius: 0 0 30px 30px;
        margin-bottom: -40px; position: relative; z-index: 1;
        box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
    }

    /* Stats Cards */
    .stats-row {
        display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;
        position: relative; z-index: 10; margin-bottom: 24px; max-width: 800px; margin-left: auto; margin-right: auto;
    }
    .stat-card {
        background: #fff; padding: 16px; border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;
        text-align: center;
    }
    .stat-val { font-size: 1.8rem; font-weight: 800; color: #111827; line-height: 1; }
    .stat-lbl { font-size: 0.8rem; color: #6b7280; font-weight: 600; text-transform: uppercase; margin-top: 4px; }

    /* Task Card List */
    .task-list { display: grid; gap: 16px; }
    .task-card {
        background: #fff; border-radius: 16px; border: 1px solid #e5e7eb;
        overflow: hidden; transition: .2s;
    }
    .task-card:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-color: #10b981; }

    .task-header {
        padding: 12px 16px; background: #f9fafb; border-bottom: 1px solid #e5e7eb;
        display: flex; justify-content: space-between; align-items: center;
    }
    .task-id { font-weight: 700; font-family: monospace; color: #6b7280; font-size: 0.9rem; }

    /* Status Badges */
    .task-status {
        font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 20px;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .st-menunggu { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
    .st-diproses { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
    .st-selesai { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
    .st-batal { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

    .task-body { padding: 16px; }
    .task-info { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px; }
    .task-icon { width: 20px; text-align: center; color: #9ca3af; margin-top: 2px; }
    .task-text { font-size: 0.9rem; color: #374151; font-weight: 500; line-height: 1.4; }

    .item-badge {
        display: inline-block; background: #f3f4f6; padding: 4px 8px;
        border-radius: 6px; font-size: 0.8rem; color: #4b5563; font-weight: 600; margin-top: 4px; border: 1px solid #e5e7eb;
    }

    .task-footer {
        padding: 12px 16px; border-top: 1px solid #e5e7eb; background: #fff;
        display: flex; gap: 10px;
    }
    .btn-task {
        flex: 1; padding: 10px; border-radius: 10px; font-weight: 700; font-size: 0.9rem;
        display: flex; align-items: center; justify-content: center; gap: 6px;
        text-decoration: none; transition: .2s; border: 1px solid transparent;
    }
    .btn-detail { background: #fff; border-color: #e5e7eb; color: #374151; }
    .btn-detail:hover { background: #f9fafb; border-color: #d1d5db; }

    .btn-nav { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
    .btn-nav:hover { background: #2563eb; color: #fff; }

    .btn-action { background: #10b981; color: #fff; border-color: #10b981; box-shadow: 0 4px 6px rgba(16,185,129,0.2); }
    .btn-action:hover { background: #059669; transform: translateY(-1px); }

    /* Empty State */
    .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
    .empty-icon { font-size: 3rem; margin-bottom: 10px; color: #e5e7eb; }
</style>
@endpush

@section('content')
    {{-- Header --}}
    <div class="page-header">
        <div style="max-width: 800px; margin: 0 auto; text-align: center; padding: 0 16px;">
            <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0;">Tugas Penjemputan</h1>
            <p style="margin: 4px 0 0; opacity: 0.9;">Pantau dan selesaikan permintaan jemput sampah.</p>
        </div>
    </div>

    <div style="max-width: 800px; margin: 0 auto;">
        {{-- Stats (Logic menghitung status dari koleksi $items yang sudah dipaginate mungkin kurang akurat,
             tapi cukup untuk overview visual di halaman ini) --}}
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-val" style="color:#f59e0b">
                    {{ \App\Models\SetoranSampah::where('status', 'menunggu')->where('metode', 'jemput')->count() }}
                </div>
                <div class="stat-lbl">Order Baru</div>
            </div>
            <div class="stat-card">
                <div class="stat-val" style="color:#3b82f6">
                    {{ \App\Models\SetoranSampah::where('petugas_id', Auth::id())->whereIn('status', ['diproses','dijemput'])->count() }}
                </div>
                <div class="stat-lbl">Tugas Saya</div>
            </div>
        </div>

        {{-- Task List --}}
        <div class="task-list">
            @forelse($items as $it)
                <div class="task-card">
                    <div class="task-header">
                        <span class="task-id">#{{ $it->id }}</span>
                        @php
                            // Normalisasi status lowercase
                            $st = strtolower($it->status);
                            $stClass = match($st) {
                                'menunggu', 'pending' => 'st-menunggu',
                                'diproses', 'dijemput' => 'st-diproses',
                                'selesai' => 'st-selesai',
                                default => 'st-batal'
                            };

                            // Label UI
                            $stLabel = match($st) {
                                'menunggu', 'pending' => 'Menunggu',
                                'diproses', 'dijemput' => 'Dalam Proses',
                                default => ucfirst($st)
                            };
                        @endphp
                        <span class="task-status {{ $stClass }}">{{ $stLabel }}</span>
                    </div>

                    <div class="task-body">
                        <div class="task-info">
                            <div class="task-icon"><i class="fa-solid fa-user"></i></div>
                            <div class="task-text">
                                <div style="font-weight:700;">{{ $it->user->name ?? 'User Umum' }}</div>
                                <div style="font-size:0.75rem; color:#6b7280;">{{ $it->created_at->diffForHumans() }}</div>
                            </div>
                        </div>

                        <div class="task-info">
                            <div class="task-icon"><i class="fa-solid fa-map-pin"></i></div>
                            <div class="task-text">
                                {{ $it->alamat ?? 'Alamat tidak tersedia' }}
                            </div>
                        </div>

                        <div class="task-info">
                            <div class="task-icon"><i class="fa-solid fa-box-open"></i></div>
                            <div class="task-text">
                                <div>Total Estimasi: <span style="color:#10b981; font-weight:700;">Rp {{ number_format($it->estimasi_total) }}</span></div>
                                <div style="margin-top:4px;">
                                    @foreach($it->items->take(2) as $d)
                                        <span class="item-badge">
                                            {{ $d->kategori->nama_sampah ?? 'Item' }}: {{ $d->jumlah }} {{ $d->satuan }}
                                        </span>
                                    @endforeach
                                    @if($it->items->count() > 2)
                                        <small class="text-muted" style="margin-left:4px;">+{{ $it->items->count() - 2 }} lainnya</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="task-footer">
                        @if($it->latitude && $it->longitude)
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $it->latitude }},{{ $it->longitude }}" target="_blank" class="btn-task btn-nav">
                                <i class="fa-solid fa-location-arrow"></i> Rute
                            </a>
                        @endif

                        <a href="{{ route('petugas.setoran.show', $it->id) }}" class="btn-task btn-detail">
                            Detail
                        </a>

                        @if(in_array($st, ['menunggu', 'pending']) && !$it->petugas_id)
                            {{-- Form Ambil Order Cepat --}}
                            <form action="{{ route('petugas.setoran.ambil', $it->id) }}" method="POST" style="flex:1; display:flex;">
                                @csrf
                                <button type="submit" class="btn-task btn-action">
                                    Ambil
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                    <h3>Tidak ada tugas saat ini</h3>
                    <p>Semua order jemputan sudah terselesaikan atau belum ada order baru masuk.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 24px; display:flex; justify-content:center;">
            {{ $items->links() }}
        </div>
    </div>
@endsection
