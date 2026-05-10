<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adjustmen extends Model
{
    use HasFactory;

    protected $table = 'adjustmen';
    protected $primaryKey = 'noadjustmen';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(Adjustmendetail::class, 'noadjustmen', 'noadjustmen');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'pengguna', 'id');
    }
}
