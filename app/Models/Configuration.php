<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
	protected $casts = [
        'default_warehouse_id' => 'string'
    ];

    public static function getKardexDays($type = 'M')
    {
        $rec = self::first();
        return ($type == 'S') ? $rec->sales_kardex_days : $rec->movements_kardex_days;
    }
}
