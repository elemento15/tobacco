<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovementConcept extends Model
{
    protected $casts = [
        'is_automatic' => 'boolean',
    ];

    public static function getByCode($code)
    {
        return self::where('code', $code)->first();
    }
}
