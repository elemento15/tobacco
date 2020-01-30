<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovementCancellation extends Model
{
    protected $fillable = ['cancel_date','user_id','comments'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
