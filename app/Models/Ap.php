<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ap extends Model
{
    use HasFactory;
    protected $table = 'ap';
    protected $primaryKey = 'noap';
    public $incrementing = false;
    public $timestamps = false;

    public function supplierRel()
    {
        return $this->belongsTo(Supplier::class, 'supplier', 'supplier');
    }

    public function penerimaan()
    {
        return $this->belongsTo(Penerimaan::class, 'nopenerimaan', 'nopenerimaan');
    }

    public function payments()
    {
        return $this->hasMany(Kaskeluar::class, 'noap', 'noap');
    }
}
