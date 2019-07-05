<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public static function getByCode($code)
    {
    	return self::where('code', $code)->first();
    }

    public static function getIdByCode($code)
    {
    	if ($role = self::getByCode($code)) {
    		return $role->id;
    	} else {
    		return false;
    	}
    }
}
