<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovementConcept extends Model
{
    public static function getByCode($code)
    {
        return self::where('code', $code)->first();
    }
}
