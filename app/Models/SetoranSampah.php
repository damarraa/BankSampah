<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetoranSampah extends Model
{
    use HasFactory;

    protected $table = 'setoran_sampah';

    protected $fillable = [
        'user_id',
        'petugas_id',
        'metode',
        'alamat',
        'latitude',
        'longitude',
        'status',
        'jadwal_jemput',
        'catatan',
        'estimasi_total',
        'petugas_latitude',
        'petugas_longitude',
        'petugas_last_seen',
    ];

    public function items()
    {
        return $this->hasMany(SetoranSampahDetail::class, 'setoran_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
