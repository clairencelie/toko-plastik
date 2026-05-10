<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adjustmendetail extends Model
{
    use HasFactory;

    protected $table = 'adjustmendetail';
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;
    protected $guarded = [];

    public function adjustmen()
    {
        return $this->belongsTo(Adjustmen::class, 'noadjustmen', 'noadjustmen');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kodebarang', 'kodebarang');
    }
}
