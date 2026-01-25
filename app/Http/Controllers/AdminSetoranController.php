<?php

namespace App\Http\Controllers;

use App\Models\SetoranSampah;
use Illuminate\Support\Facades\Auth;

class AdminSetoranController extends Controller
{
    public function index()
    {
        $items = SetoranSampah::with(['items.kategori','user','petugas'])
            ->latest()
            ->paginate(15);

        return view('admin.setoran.index', compact('items'));
    }

    public function show($id)
    {
        $setoran = SetoranSampah::with(['items.kategori','user','petugas'])->findOrFail($id);
        return view('admin.setoran.show', compact('setoran'));
    }

    // JSON lokasi petugas untuk admin
    public function petugasLocation($id)
    {
        $setoran = SetoranSampah::with('petugas')->findOrFail($id);

        return response()->json([
            'petugas_id' => $setoran->petugas_id,
            'petugas_name' => optional($setoran->petugas)->name,
            'petugas_latitude' => $setoran->petugas_latitude,
            'petugas_longitude' => $setoran->petugas_longitude,
            'petugas_last_seen' => $setoran->petugas_last_seen,
            'status' => $setoran->status,
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
            ->with(['user','petugas'])
            ->select([
                'id','user_id','petugas_id','status','alamat',
                'latitude','longitude',
                'petugas_latitude','petugas_longitude','petugas_last_seen',
                'created_at'
            ])
            ->latest()
            ->get();

        return response()->json([
            'items' => $rows->map(function($s) use ($currentUserId){
                return [
                    'id' => $s->id,
                    'status' => $s->status,
                    'alamat' => $s->alamat,
                    'lat' => (float)$s->latitude,
                    'lng' => (float)$s->longitude,
                    'created_at' => optional($s->created_at)->toDateTimeString(),

                    'user_id' => $s->user_id,
                    'user_name' => optional($s->user)->name,
                    'is_my_data' => ($s->user_id == $currentUserId), // flag untuk bedakan data sendiri

                    'petugas_id' => $s->petugas_id,
                    'petugas_name' => optional($s->petugas)->name,
                    'petugas_lat' => $s->petugas_latitude ? (float)$s->petugas_latitude : null,
                    'petugas_lng' => $s->petugas_longitude ? (float)$s->petugas_longitude : null,
                    'petugas_last_seen' => $s->petugas_last_seen,
                ];
            })->values()
        ]);
    }
}
