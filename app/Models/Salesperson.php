<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salesperson extends Model
{
    protected $fillable = ['name', 'mobile'];

    public function prices()
    {
        return $this->hasMany('App\Models\Price');
    }
}
