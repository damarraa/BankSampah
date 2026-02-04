<?php
namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use App\Models\PenjualanSampah;
use Illuminate\Http\Request;

class AdminPenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penjualan = PenjualanSampah::with('kategori.masterKategori')
            ->orderBy('tanggal_penjualan', 'desc')
            ->paginate(10);

        $kategoriList = KategoriSampah::with('masterKategori')->get()
            ->map(function ($item) {
                $item->sisa_stok = $item->stok_aktual;
                return $item;
            });

        return view('admin.penjualan.index', compact('penjualan', 'kategoriList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori_sampah_id' => 'required|exists:kategori_sampah,id',
            'pembeli'            => 'required|string|max:255',
            'jumlah'             => 'required|numeric|min:0.1',
            'harga_jual'         => 'required|numeric|min:0',
            'tanggal_penjualan'  => 'required|date',
        ]);

        $kategori = KategoriSampah::findOrFail($request->kategori_sampah_id);

        if ($request->jumlah > $kategori->stok_aktual) {
            return back()->with('error', "Stok tidak cukup! Sisa stok: {$kategori->stok_aktual} {$kategori->jenis_satuan}")
                ->withInput();
        }

        PenjualanSampah::create([
            'kategori_sampah_id' => $request->kategori_sampah_id,
            'pembeli'            => $request->pembeli,
            'jumlah'             => $request->jumlah,
            'harga_jual'         => $request->harga_jual,
            'total_pendapatan'   => $request->jumlah * $request->harga_jual,
            'tanggal_penjualan'  => $request->tanggal_penjualan,
            'catatan'            => $request->catatan,
        ]);

        return back()->with('success', 'Penjualan berhasil dicatat. Stok gudang berkurang.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $penjualan = PenjualanSampah::findOrFail($id);
        $penjualan->delete();
        return back()->with('success', 'Data penjualan dihapus. Stok dikembalikan.');
    }
}
