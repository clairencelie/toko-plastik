<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salesman extends Model
{
    use HasFactory;

    protected $table = 'salesman';
    protected $primaryKey = 'salesman';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'keterangan',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'salesman_id', 'salesman');
    }
}
