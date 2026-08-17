<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name', 'brand_type_id', 'packs_per_box', 'cost', 'price'];

    protected $casts = [
    	'cost'  => 'float',
        'price' => 'float'
    ];

    public function prices()
    {
        return $this->hasMany('App\Models\Price');
    }

    public function stocks()
    {
        return $this->hasMany('App\Models\Stock');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\BrandType', 'brand_type_id');
    }
}
