<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasmasuk extends Model
{
    use HasFactory;
    protected $table = 'kasmasuk';
    protected $primaryKey = 'nokasmasuk';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function ar()
    {
        return $this->belongsTo(Ar::class, 'noref', 'noar');
    }
}
