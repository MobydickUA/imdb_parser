<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'id',
        'name',
        'year',
    ];
    public $timestamps = [];

    public function actor()
    {
        return $this->belongsTo('App\Actor');
    }
}
