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

    public function masterKategori()
    {
        return $this->belongsTo(MasterKategoriSampah::class, 'master_kategori_id');
    }
}
