<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutasibarang extends Model
{
    use HasFactory;
    protected $table = 'mutasibarang';
    protected $primaryKey = 'kodebarang';
    public $incrementing = false;
    public $timestamps = false;
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kodebarang', 'kodebarang');
    }
}
