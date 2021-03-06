<?php

namespace App;

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
        return $this->hasMany('App\Price');
    }

    public function stocks()
    {
        return $this->hasMany('App\Stock');
    }

    public function type()
    {
        return $this->belongsTo('App\BrandType', 'brand_type_id');
    }
}
