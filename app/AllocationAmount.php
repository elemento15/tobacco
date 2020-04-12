<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllocationAmount extends Model
{
    public $timestamps = false;

    protected $casts = [
    	'cost'  => 'float',
    	'price' => 'float'
    ];

    public function allocation()
    {
        return $this->belongsTo('App\Allocation');
    }
}
