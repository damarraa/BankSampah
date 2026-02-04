<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanSampah extends Model
{
    use HasFactory;

    protected $table = 'penjualan_sampahs';

    protected $fillable = [
        'kategori_sampah_id',
        'pembeli',
        'jumlah',
        'harga_jual',
        'total_pendapatan',
        'tanggal_penjualan',
        'bukti_transaksi',
        'catatan',
    ];

    protected $casts = [
        'tanggal_penjualan' => 'date',
    ];

    // Relasi ke Master Kategori
    public function kategori()
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_sampah_id');
    }
}
