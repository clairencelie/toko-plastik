<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penerimaan extends Model
{
    use HasFactory;
    protected $table = 'penerimaan';
    protected $primaryKey = 'nopenerimaan';
    public $incrementing = false;
    public $timestamps = false;

    public function details()
    {
        return $this->hasMany(Penerimaandetail::class, 'nopenerimaan', 'nopenerimaan');
    }

    public function supplierRel()
    {
        return $this->belongsTo(Supplier::class, 'supplier', 'supplier');
    }
}
