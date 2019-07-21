<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name', 'packs_per_box', 'cost'];

    protected $casts = [
    	'cost' => 'float'
    ];

    public function prices()
    {
        return $this->hasMany('App\Price');
    }

    public function stocks()
    {
        return $this->hasMany('App\Stock');
    }
}
