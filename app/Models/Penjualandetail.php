<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualandetail extends Model
{
    use HasFactory;
    protected $table = 'penjualandetail';
    public $incrementing = false;
    public $timestamps = false;

    public function header()
    {
        return $this->belongsTo(Penjualan::class, 'nopenjualan', 'nopenjualan');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kodebarang', 'kodebarang');
    }
}
