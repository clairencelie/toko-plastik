<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fifostock extends Model
{
    use HasFactory;
    protected $table = 'fifostock';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'nobukti',
        'kodebarang',
        'nourut',
        'waktu',
        'harga',
        'masuk',
        'keluar',
        'saldo',
        'returmasuk',
        'returkeluar',
        'hargabonus',
    ];

    // If no primary key or not auto-incrementing, set primary key manually
    // protected $primaryKey = 'nobuktu'; 
    // public $incrementing = false; 
}
