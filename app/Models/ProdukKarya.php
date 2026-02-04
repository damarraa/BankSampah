<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukKarya extends Model
{
    protected $guarded = ['id'];
    protected $casts   = ['tanggal_dibuat' => 'date'];

    public function bahanBaku()
    {
        return $this->hasMany(ProdukKaryaDetail::class, 'produk_karya_id');
    }
}
