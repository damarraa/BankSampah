<?php
namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use App\Models\SetoranSampah;
use App\Models\SetoranSampahDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminStokController extends Controller
{
    public function index()
    {
        // Ambil kategori sampah beserta jumlah beratnya dari transaksi yang 'selesai'
        $stok = KategoriSampah::with('masterKategori')
            ->withSum(['setoranDetail as total_berat' => function ($query) {
                $query->whereHas('setoran', function ($q) {
                    $q->where('status', 'selesai');
                });
            }], 'jumlah')
            ->withSum(['setoranDetail as total_nilai' => function ($query) {
                $query->whereHas('setoran', function ($q) {
                    $q->where('status', 'selesai');
                });
            }], 'subtotal')
            ->get()
        // Urutkan dari stok terbanyak
            ->sortByDesc('total_berat');

        $kategoriList = KategoriSampah::all();
        return view('admin.stok.index', compact('stok', 'kategoriList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_sampah_id' => 'required|exists:kategori_sampah,id',
            'berat' => 'required|numeric|min:0.1',
            'harga_total' => 'required|numeric|min:0',
            'catatan' => 'nullable|string'
        ]);

        DB::transaction(function() use ($request) {
            $setoran = SetoranSampah::create([
                'user_id' => Auth::id(),
                'metode' => 'antar',
                'status' => 'selesai',
                'estimasi_total' => $request->harga_total,
                'catatan' => 'Manual Input Stok: ' . ($request->catatan ?? '-'),
                'alamat' => 'Gudang Utama (Manual Input)',
            ]);

            $kategori = KategoriSampah::find($request->kategori_sampah_id);
            $hargaSatuan = $request->berat > 0 ? ($request->harga_total / $request->berat) : 0;

            SetoranSampahDetail::create([
                'setoran_id' => $setoran->id,
                'kategori_sampah_id' => $kategori->id,
                'jumlah' => $request->berat,
                'satuan' => $kategori->jenis_satuan,
                'harga_satuan' => $hargaSatuan,
                'subtotal' => $request->harga_total
            ]);
        });

        return back()->with('success', 'Stok berhasil ditambahkan secara manual.');
    }

    // Kita arahkan Edit ke Controller KategoriSampah yang sudah ada (supaya tidak duplikat logic)
    public function edit($id)
    {
        return redirect()->route('kategori_sampah.edit', $id);
    }

    // Delete juga diarahkan atau ditangani di sini (hati-hati menghapus kategori yang ada isinya)
    public function destroy($id)
    {
        // Logic delete biasanya ada di KategoriSampahController,
        // tapi jika mau di sini, pastikan cek relasi dulu.
        return redirect()->route('kategori_sampah.index')->with('error', 'Silakan hapus dari menu Master Data Kategori.');
    }
}
