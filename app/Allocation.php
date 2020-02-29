<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    protected $fillable = ['rec_date','type','salesperson_id','warehouse_id','user_id','comments'];
}
