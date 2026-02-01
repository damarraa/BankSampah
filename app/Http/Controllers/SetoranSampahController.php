<?php
namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use App\Models\SetoranSampah;
use App\Models\SetoranSampahDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetoranSampahController extends Controller
{
    // =========================
    // DASHBOARD USER
    // - master kategori grouping
    // - pendapatan status selesai
    // - filter tahun
    // =========================
    public function dashboard(Request $request)
    {
        $tahun = $request->query('tahun'); // optional

        $kategori = KategoriSampah::with('masterKategori')
            ->orderBy('nama_sampah')
            ->get();

        $groups     = $kategori->groupBy(fn($k) => $k->masterKategori?->nama_kategori ?? 'Lainnya');
        $totalCount = $kategori->count();
        $featured   = $kategori->take(6);

        // pendapatan hanya status selesai, bisa filter tahun
        $qSelesai = SetoranSampah::query()
            ->where('user_id', Auth::id())
            ->where('status', 'selesai');

        if (! empty($tahun)) {
            $qSelesai->whereYear('created_at', $tahun);
        }

        $totalPendapatan = (int) (clone $qSelesai)
            ->when($tahun, function ($q) use ($tahun) {
                $q->whereYear('created_at', $tahun);
            })
            ->sum('estimasi_total');

        // pendapatan per tahun (untuk dropdown + list)
        $pendapatanPerTahun = SetoranSampah::query()
            ->where('user_id', Auth::id())
            ->where('status', 'selesai')
            ->selectRaw('YEAR(created_at) as tahun, SUM(estimasi_total) as total')
            ->groupByRaw('YEAR(created_at)')
            ->orderBy('tahun', 'desc')
            ->get();

        $listTahun = SetoranSampah::where('user_id', Auth::id())
            ->selectRaw('YEAR(created_at) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // $listTahun = $pendapatanPerTahun->pluck('tahun')->values();

        if ($listTahun->isEmpty()) {
            $listTahun = collect([date('Y')]);
        }

        // setoran per tahun (count) tetap boleh tampil
        $setoranPerTahun = SetoranSampah::query()
            ->where('user_id', Auth::id())
            ->selectRaw('YEAR(created_at) as tahun, COUNT(*) as total')
            ->groupByRaw('YEAR(created_at)')
            ->orderBy('tahun', 'desc')
            ->get();

        return view('user.dashboard', compact(
            'kategori',
            'groups',
            'totalCount',
            'featured',
            'totalPendapatan',
            'pendapatanPerTahun',
            'listTahun',
            'tahun',
            'setoranPerTahun'
        ));
    }

    // =========================
    // LIST SETORAN USER
    // =========================
    public function index()
    {
        $items = SetoranSampah::with(['items.kategori.masterKategori', 'petugas'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.setoran.index', compact('items'));
    }

    // =========================
    // FORM BUAT SETORAN
    // =========================
    public function create(Request $request)
    {
        $selectedId = $request->query('kategori_sampah_id');

        $kategori = KategoriSampah::with('masterKategori')
            ->orderBy('nama_sampah')
            ->get();

        // data untuk JS select
        $kategoriData = $kategori->map(function ($k) {
            return [
                'id'       => $k->id,
                'nama'     => $k->nama_sampah,
                'kategori' => $k->masterKategori?->nama_kategori, // ✅ master kategori
                'harga'    => is_numeric($k->harga_satuan) ? (int) $k->harga_satuan : 0,
                'satuan'   => $k->jenis_satuan ?? '',
            ];
        })->values();

        return view('user.setoran.create', compact('kategori', 'selectedId', 'kategoriData'));
    }

    // =========================
    // SIMPAN SETORAN (MULTI ITEMS)
    // =========================
    public function store(Request $request)
    {
        $userId = Auth::id();
        if (! $userId) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'metode'                     => ['required', 'in:antar,jemput'],
            'alamat'                     => ['nullable', 'string', 'max:255'],
            'latitude'                   => ['nullable', 'numeric'],
            'longitude'                  => ['nullable', 'numeric'],
            'jadwal_jemput'              => ['nullable', 'date'],
            'catatan'                    => ['nullable', 'string'],

            'items'                      => ['required', 'array', 'min:1'],
            'items.*.kategori_sampah_id' => ['required', 'exists:kategori_sampah,id'],
            'items.*.jumlah'             => ['required', 'numeric', 'min:0.01'],
        ]);

        if ($data['metode'] === 'jemput') {
            if (empty($data['alamat'])) {
                return back()->withErrors(['alamat' => 'Alamat wajib jika jemput.'])->withInput();
            }
            if (empty($data['latitude']) || empty($data['longitude'])) {
                return back()->withErrors(['latitude' => 'Pilih titik jemput di peta.'])->withInput();
            }
        }

        return DB::transaction(function () use ($data, $userId) {

            $setoran = SetoranSampah::create([
                'user_id'        => $userId,
                'metode'         => $data['metode'],
                'alamat'         => $data['alamat'] ?? null,
                'latitude'       => $data['latitude'] ?? null,
                'longitude'      => $data['longitude'] ?? null,
                'jadwal_jemput'  => $data['jadwal_jemput'] ?? null,
                'catatan'        => $data['catatan'] ?? null,
                'status'         => 'pending',
                'estimasi_total' => 0,
            ]);

            $estimasiTotal = 0;

            foreach ($data['items'] as $row) {
                $k = KategoriSampah::findOrFail($row['kategori_sampah_id']);

                $harga  = is_numeric($k->harga_satuan) ? (int) $k->harga_satuan : 0;
                $satuan = $k->jenis_satuan ?? null;

                $jumlah   = (float) $row['jumlah'];
                $subtotal = (int) round($jumlah * $harga);

                SetoranSampahDetail::create([
                    'setoran_id'         => $setoran->id,
                    'kategori_sampah_id' => $k->id,
                    'jumlah'             => $jumlah,
                    'satuan'             => $satuan,
                    'harga_satuan'       => $harga,
                    'subtotal'           => $subtotal,
                ]);

                $estimasiTotal += $subtotal;
            }

            $setoran->update(['estimasi_total' => $estimasiTotal]);

            return redirect()->route('user.setoran.index')
                ->with('success', 'Setoran berhasil dibuat.');
        });
    }

    // =========================
    // DETAIL SETORAN
    // =========================
    public function show($id)
    {
        $setoran = SetoranSampah::with(['items.kategori.masterKategori', 'petugas'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.setoran.show', compact('setoran'));
    }

    // =========================
    // JSON TRACKING PETUGAS
    // =========================
    public function petugasLocation($id)
    {
        $setoran = SetoranSampah::with('petugas')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'petugas_id'        => $setoran->petugas_id,
            'petugas_name'      => optional($setoran->petugas)->name,
            'petugas_latitude'  => $setoran->petugas_latitude ? (float) $setoran->petugas_latitude : null,
            'petugas_longitude' => $setoran->petugas_longitude ? (float) $setoran->petugas_longitude : null,
            'petugas_last_seen' => $setoran->petugas_last_seen ? $setoran->petugas_last_seen->format('Y-m-d H:i:s') : null,
            'status'            => $setoran->status,
        ]);
    }
    public function mapUser()
    {
        // hanya render halaman peta
        return view('user.map');
    }

    public function mapUserData()
    {
        $userId = Auth::id();

        // ambil semua setoran jemput milik user yang punya koordinat
        $rows = SetoranSampah::query()
            ->where('user_id', $userId)
            ->where('metode', 'jemput')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('petugas')
            ->select([
                'id', 'status', 'alamat', 'latitude', 'longitude',
                'petugas_id', 'petugas_latitude', 'petugas_longitude', 'petugas_last_seen',
                'created_at',
            ])
            ->latest()
            ->get();

        return response()->json([
            'items' => $rows->map(function ($s) {
                return [
                    'id'                => $s->id,
                    'status'            => $s->status,
                    'alamat'            => $s->alamat,
                    'lat'               => (float) $s->latitude,
                    'lng'               => (float) $s->longitude,
                    'created_at'        => optional($s->created_at)->toDateTimeString(),
                    'petugas_id'        => $s->petugas_id,
                    'petugas_name'      => optional($s->petugas)->name,
                    'petugas_lat'       => $s->petugas_latitude ? (float) $s->petugas_latitude : null,
                    'petugas_lng'       => $s->petugas_longitude ? (float) $s->petugas_longitude : null,
                    'petugas_last_seen' => $s->petugas_last_seen ? $s->petugas_last_seen->format('Y-m-d H:i:s') : null,
                ];
            })->values(),
        ]);
    }
}
