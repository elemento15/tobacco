<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
	protected $casts = [
        'default_warehouse_id' => 'string',
        'allocation_warehouse_id' => 'string',
    ];

	public function allocation_warehouse()
    {
        return $this->belongsTo('App\Warehouse');
    }


    public static function getAllocationWarehouse()
    {
    	$record = self::first();
    	return $record->allocation_warehouse;
    }

    public static function getKardexDays($type = 'M')
    {
        $rec = self::first();
        return ($type == 'S') ? $rec->sales_kardex_days : $rec->movements_kardex_days;
    }
}
