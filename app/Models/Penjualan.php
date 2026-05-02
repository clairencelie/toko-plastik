<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;
    protected $table = 'penjualan';
    protected $primaryKey = 'nopenjualan';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(Penjualandetail::class, 'nopenjualan', 'nopenjualan');
    }

    public function pelangganRel()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan', 'kodepelanggan');
    }
}
