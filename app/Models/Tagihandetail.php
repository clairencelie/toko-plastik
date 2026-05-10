<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihandetail extends Model
{
    use HasFactory;

    protected $table = 'tagihandetail';
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;
    protected $guarded = [];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'notagihan', 'notagihan');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'nopenjualan', 'nopenjualan');
    }
}
