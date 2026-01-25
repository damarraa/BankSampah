<?php

namespace App\Http\Controllers;

use App\Models\SetoranSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasSetoranController extends Controller
{
    public function index()
    {
        $items = SetoranSampah::with(['items.kategori','user'])
            ->where('metode','jemput')
            ->whereIn('status',['pending','diproses'])
            ->latest()
            ->paginate(10);

        return view('petugas.setoran.index', compact('items'));
    }

    public function show($id)
    {
        $setoran = SetoranSampah::with(['items.kategori','user','petugas'])->findOrFail($id);
        return view('petugas.setoran.show', compact('setoran'));
    }

    // ✅ petugas ambil order (assign dirinya)
    public function ambil($id)
    {
        $setoran = SetoranSampah::findOrFail($id);

        if ($setoran->metode !== 'jemput') abort(403);
        if ($setoran->status !== 'pending') {
            return back()->with('error','Setoran sudah diproses.');
        }

        $setoran->update([
            'petugas_id' => Auth::id(),
            'status' => 'diproses',
        ]);

        return back()->with('success','Order berhasil diambil.');
    }

    // ✅ update status: diproses/selesai/ditolak
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required','in:diproses,selesai,ditolak'],
        ]);

        $setoran = SetoranSampah::findOrFail($id);

        // hanya petugas yang assigned yang boleh update
        if ($setoran->petugas_id !== Auth::id()) abort(403);

        $setoran->update(['status' => $request->status]);

        return back()->with('success','Status diperbarui.');
    }

    // ✅ update lokasi realtime dari HP petugas (watchPosition)
    public function updateLocation(Request $request, $id)
    {
        $request->validate([
            'lat' => ['required','numeric'],
            'lng' => ['required','numeric'],
        ]);

        $setoran = SetoranSampah::findOrFail($id);

        if ($setoran->petugas_id !== Auth::id()) abort(403);

        $setoran->update([
            'petugas_latitude' => $request->lat,
            'petugas_longitude' => $request->lng,
            'petugas_last_seen' => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    // ✅ map view untuk petugas - tampilkan semua titik jemput
    public function map()
    {
        return view('petugas.map');
    }

    // ✅ data untuk map petugas - semua setoran jemput yang bisa diambil
    public function mapData()
    {
        $petugasId = Auth::id();

        $rows = SetoranSampah::query()
            ->where('metode', 'jemput')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['user'])
            ->select([
                'id','user_id','petugas_id','status','alamat',
                'latitude','longitude',
                'created_at'
            ])
            ->whereIn('status', ['pending', 'diproses', 'diambil', 'selesai'])
            ->latest()
            ->get();

        return response()->json([
            'items' => $rows->map(function($s) use ($petugasId){
                return [
                    'id' => $s->id,
                    'status' => $s->status,
                    'alamat' => $s->alamat,
                    'lat' => (float)$s->latitude,
                    'lng' => (float)$s->longitude,
                    'created_at' => optional($s->created_at)->toDateTimeString(),
                    'user_id' => $s->user_id,
                    'user_name' => optional($s->user)->name,
                    'petugas_id' => $s->petugas_id,
                    'is_assigned_to_me' => ($s->petugas_id == $petugasId), // apakah assigned ke petugas ini
                ];
            })->values()
        ]);
    }
}
