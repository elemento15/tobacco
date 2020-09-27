<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    protected $fillable = ['rec_date','type','brand_type_id','salesperson_id','warehouse_id','user_id','doc_number','comments'];


    public function salesperson()
    {
        return $this->belongsTo('App\Salesperson');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function cancellation()
    {
        return $this->belongsTo('App\AllocationCancellation');
    }

    public function brand_type()
    {
        return $this->belongsTo('App\BrandType');
    }

    public function amount()
    {
        return $this->hasOne('App\AllocationAmount');
    }

    public function details()
    {
        return $this->hasMany('App\AllocationBrand');
    }
}
