<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukKaryaDetail extends Model
{
    protected $guarded = ['id'];

    public function kategori()
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_sampah_id');
    }
}
