<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'kodebarang';
    public $incrementing = false;
    public $timestamps = false;

    public function kelompokRel()
    {
        return $this->belongsTo(Kelompok::class, 'kelompok', 'kelompok');
    }

    public function kemasanRel()
    {
        return $this->belongsTo(Kemasan::class, 'kemasan', 'kemasan');
    }

    public function satuanRel()
    {
        return $this->belongsTo(Satuan::class, 'satuan', 'satuan');
    }

    public function supplierRel()
    {
        return $this->belongsTo(Supplier::class, 'supplier', 'supplier');
    }
}
