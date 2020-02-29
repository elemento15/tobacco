<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{

	public function allocation_warehouse()
    {
        return $this->belongsTo('App\Warehouse');
    }


    public static function getAllocationWarehouse()
    {
    	$record = self::first();
    	return $record->allocation_warehouse;
    }
}
