<?php

namespace App;

//use App\Warehouse;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
	protected $fillable = ['mov_date','type','warehouse_id','concept_id','transfer_to','user_id','comments'];

    protected $appends = ['transfer_to_warehouse','transfer_from_warehouse'];


	public function warehouse()
    {
        return $this->belongsTo('App\Warehouse');
    }

    public function concept()
    {
        return $this->belongsTo('App\MovementConcept');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function cancellation()
    {
        return $this->belongsTo('App\MovementCancellation');
    }

    public function details()
    {
        return $this->hasMany('App\MovementBrand');
    }

    /**
     * Accessor for transfer_to
     */
    public function getTransferToWarehouseAttribute()
    {
    	if ($this->transfer_to) {
    		$mov = Movement::find($this->transfer_to);
    		return $mov->warehouse->name;
    	} else {
    		return null;
    	}
    }

    /**
     * Accessor for transfer_from
     */
    public function getTransferFromWarehouseAttribute()
    {
    	if ($this->transfer_from) {
            $mov = Movement::find($this->transfer_from);
    		return $mov->warehouse->name;
    	} else {
    		return null;
    	}
    }
}
