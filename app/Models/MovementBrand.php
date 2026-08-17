<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementBrand extends Model
{
	public $timestamps = false;


    public function movement()
    {
        return $this->belongsTo('App\Models\Movement');
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand');
    }
}
