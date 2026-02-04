<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keuangan extends Model
{
    protected $guarded = ['id'];
    protected $casts   = ['tanggal' => 'date'];
}
