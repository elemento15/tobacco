<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovementBrand extends Model
{
	public $timestamps = false;


    public function movement()
    {
        return $this->belongsTo('App\Movement');
    }

    public function brand()
    {
        return $this->belongsTo('App\Brand');
    }
}
