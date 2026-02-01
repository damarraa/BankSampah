<?php
namespace App\Http\Controllers;

use App\Models\SetoranSampah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSetoranController extends Controller
{
    public function index(Request $request)
    {
        // Parameter Filter
        $status    = $request->query('status');
        $metode    = $request->query('metode');
        $petugasId = $request->query('petugas');
        $q         = $request->query('q');

        $query = SetoranSampah::with(['items.kategori', 'user', 'petugas'])
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }
        if ($metode) {
            $query->where('metode', $metode);
        }
        if ($petugasId) {
            $query->where('petugas_id', $petugasId);
        }
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('user', function ($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                })
                    ->orWhere('id', 'like', "%{$q}%");
            });
        }

        $items = $query->paginate(15)->withQueryString();

        $countPending = SetoranSampah::where('status', 'menunggu')->count();
        $countProcess = SetoranSampah::whereIn('status', ['diproses', 'dijemput'])->count();
        $countDone    = SetoranSampah::where('status', 'selesai')->count();
        $countCancel  = SetoranSampah::whereIn('status', ['dibatalkan', 'ditolak'])->count();

        $totalAll   = SetoranSampah::count();
        $totalValue = SetoranSampah::sum('estimasi_total');

        $petugasList = User::where('role', 'petugas')->orderBy('name')->get();

        return view('admin.setoran.index', compact(
            'items',
            'status', 'metode', 'petugasId', 'q',
            'countPending', 'countProcess', 'countDone', 'countCancel', 'totalAll', 'totalValue',
            'petugasList'
        ));
    }

    public function show($id)
    {
        $setoran = SetoranSampah::with(['items.kategori', 'user', 'petugas'])->findOrFail($id);
        return view('admin.setoran.show', compact('setoran'));
    }

    // JSON lokasi petugas untuk admin
    public function petugasLocation($id)
    {
        $setoran = SetoranSampah::with('petugas')->findOrFail($id);

        return response()->json([
            'petugas_id'        => $setoran->petugas_id,
            'petugas_name'      => optional($setoran->petugas)->name,
            'petugas_latitude'  => $setoran->petugas_latitude,
            'petugas_longitude' => $setoran->petugas_longitude,
            'petugas_last_seen' => $setoran->petugas_last_seen,
            'status'            => $setoran->status,
        ]);
    }
    public function mapAdmin()
    {
        return view('admin.map');
    }

    public function mapAdminData()
    {
        $currentUserId = Auth::id(); // untuk membedakan data user sendiri

        $rows = SetoranSampah::query()
            ->where('metode', 'jemput')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['user', 'petugas'])
            ->select([
                'id', 'user_id', 'petugas_id', 'status', 'alamat',
                'latitude', 'longitude',
                'petugas_latitude', 'petugas_longitude', 'petugas_last_seen',
                'created_at',
            ])
            ->latest()
            ->get();

        return response()->json([
            'items' => $rows->map(function ($s) use ($currentUserId) {
                return [
                    'id'                => $s->id,
                    'status'            => $s->status,
                    'alamat'            => $s->alamat,
                    'lat'               => (float) $s->latitude,
                    'lng'               => (float) $s->longitude,
                    'created_at'        => optional($s->created_at)->toDateTimeString(),

                    'user_id'           => $s->user_id,
                    'user_name'         => optional($s->user)->name,
                    'is_my_data'        => ($s->user_id == $currentUserId), // flag untuk bedakan data sendiri

                    'petugas_id'        => $s->petugas_id,
                    'petugas_name'      => optional($s->petugas)->name,
                    'petugas_lat'       => $s->petugas_latitude ? (float) $s->petugas_latitude : null,
                    'petugas_lng'       => $s->petugas_longitude ? (float) $s->petugas_longitude : null,
                    'petugas_last_seen' => $s->petugas_last_seen,
                ];
            })->values(),
        ]);
    }
}
