<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllocationBrand extends Model
{
    public $timestamps = false;


    public function allocation()
    {
        return $this->belongsTo('App\Allocation');
    }

    public function brand()
    {
        return $this->belongsTo('App\Brand');
    }
}
