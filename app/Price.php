<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
	protected $fillable = ['brand_id', 'salesperson_id', 'price'];

    public function brand()
    {
        return $this->belongsTo('App\Brand');
    }

    public function salesperson()
    {
        return $this->belongsTo('App\Salesperson');
    }
}
