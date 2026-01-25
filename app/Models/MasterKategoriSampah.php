<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKategoriSampah extends Model
{
    use HasFactory;

    protected $table = 'master_kategori_sampah';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function kategoriSampah()
    {
        return $this->hasMany(KategoriSampah::class, 'master_kategori_id');
    }
}
