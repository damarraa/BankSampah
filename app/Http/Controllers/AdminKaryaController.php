<?php
namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use App\Models\ProdukKarya;
use App\Models\ProdukKaryaDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminKaryaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $karya = ProdukKarya::with('bahanBaku.kategori')->latest()->paginate(10);

        $stokBahan = KategoriSampah::with('masterKategori')->get()
            ->map(function ($item) {
                $item->sisa_stok = $item->stok_aktual;
                return $item;
            });

        return view('admin.karya.index', compact('karya', 'stokBahan'));
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
            'nama_karya'          => 'required|string',
            'harga_jual'          => 'required|numeric',
            'tanggal_dibuat'      => 'required|date',
            'items'               => 'required|array|min:1',
            'items.*.kategori_id' => 'required|exists:kategori_sampah,id',
            'items.*.jumlah'      => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {
            $produk = ProdukKarya::create([
                'nama_karya'     => $request->nama_karya,
                'pembeli'        => $request->pembeli,
                'harga_jual'     => $request->harga_jual,
                'tanggal_dibuat' => $request->tanggal_dibuat,
                'deskripsi'      => $request->deskripsi,
            ]);

            foreach ($request->items as $item) {
                $kategori = KategoriSampah::find($item['kategori_id']);
                if ($item['jumlah'] > $kategori->stok_aktual) {
                    throw new \Exception("Stok {$kategori->nama_sampah} tidak cukup! Tersisa: {$kategori->stok_aktual}");
                }

                ProdukKaryaDetail::create([
                    'produk_karya_id'    => $produk->id,
                    'kategori_sampah_id' => $item['kategori_id'],
                    'jumlah_pakai'       => $item['jumlah'],
                ]);
            }
        });

        return back()->with('success', 'Produk berhasil dibuat & stok bahan baku dikurangi.');
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
        ProdukKarya::destroy($id);
        return back()->with('success', 'Data dihapus & stok bahan baku dikembalikan.');
    }
}
