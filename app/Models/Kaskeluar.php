<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kaskeluar extends Model
{
    use HasFactory;
    protected $table = 'kaskeluar';
    protected $primaryKey = 'nokaskeluar';
    public $incrementing = false;
    public $timestamps = false;

    public function ap()
    {
        return $this->belongsTo(Ap::class, 'noap', 'noap');
    }
}
