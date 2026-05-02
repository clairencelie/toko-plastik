<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penerimaandetail extends Model
{
    use HasFactory;
    protected $table = 'penerimaandetail';
    // Laravel does not support composite primary keys [nopenerimaan, nourut]
    // We will treat it as having no single primary key for standard Eloquent usage
    public $incrementing = false;
    public $timestamps = false;

    public function header()
    {
        return $this->belongsTo(Penerimaan::class, 'nopenerimaan', 'nopenerimaan');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kodebarang', 'kodebarang');
    }
}
