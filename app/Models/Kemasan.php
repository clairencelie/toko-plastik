<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kemasan extends Model
{
    use HasFactory;
    protected $table = 'kemasan';
    protected $primaryKey = 'kemasan';
    public $incrementing = false;
    public $timestamps = false;
}
