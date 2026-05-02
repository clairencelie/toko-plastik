<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ar extends Model
{
    use HasFactory;
    protected $table = 'ar';
    protected $primaryKey = 'noar';
    public $incrementing = false;
    public $timestamps = false;

    public function pelangganRel()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan', 'kodepelanggan');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'nopenjualan', 'nopenjualan');
    }

    public function payments()
    {
        return $this->hasMany(Kasmasuk::class, 'noar', 'noar');
    }
}
