<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSampah extends Model
{
    use HasFactory;

    protected $table = 'kategori_sampah';

    protected $fillable = [
        'nama_sampah',
        'master_kategori_id',
        'deskripsi',
        'harga_satuan',
        'jenis_satuan',
        'gambar_sampah',
    ];

    /**
     * Relasi ke Master Kategori
     */
    public function masterKategori()
    {
        return $this->belongsTo(MasterKategoriSampah::class, 'master_kategori_id');
    }

    /**
     * Relasi ke Detail Setoran
     */
    public function setoranDetail()
    {
        return $this->hasMany(SetoranSampahDetail::class, 'kategori_sampah_id');
    }

    // Relasi Stok Keluar (Penjualan)
    public function penjualan()
    {
        return $this->hasMany(PenjualanSampah::class, 'kategori_sampah_id');
    }

    // Relasi Produksi
    public function pemakaianProduksi()
    {
        return $this->hasMany(ProdukKaryaDetail::class, 'kategori_sampah_id');
    }

    // Helper Function: Hitung Stok Aktual (Masuk - Keluar)
    public function getStokAktualAttribute()
    {
        // Hitung total masuk (dari setoran yang statusnya selesai)
        $masuk = $this->setoranDetail()
            ->whereHas('setoran', function ($q) {
                $q->where('status', 'selesai');
            })->sum('jumlah');

        // Hitung total keluar
        $keluarJual = $this->penjualan()->sum('jumlah');

        // 3. Keluar via Produksi Karya (NEW)
        $keluarProduksi = $this->pemakaianProduksi()->sum('jumlah_pakai');

        return max(0, $masuk - ($keluarJual + $keluarProduksi));
    }
}
