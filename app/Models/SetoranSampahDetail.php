<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetoranSampahDetail extends Model
{
    use HasFactory;

    protected $table = 'setoran_sampah_detail';

    protected $fillable = [
        'setoran_id',
        'kategori_sampah_id',
        'jumlah',
        'satuan',
        'harga_satuan',
        'subtotal',
    ];

    public function setoran()
    {
        return $this->belongsTo(SetoranSampah::class, 'setoran_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_sampah_id');
    }
}
