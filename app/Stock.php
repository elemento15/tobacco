<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['brand_id', 'warehouse_id', 'quantity'];

	public $timestamps = false;

    public function brand()
    {
        return $this->belongsTo('App\Brand');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse');
    }


    public static function addStock($brand_id, $warehouse_id, $quantity = 0)
    {
        self::updateStock($brand_id, $warehouse_id, $quantity);
    }


    public static function substractStock($brand_id, $warehouse_id, $quantity = 0)
    {
    	self::updateStock($brand_id, $warehouse_id, $quantity * -1);
    }


    private static function updateStock($brand_id, $warehouse_id, $quantity)
    {
    	$stock = self::firstOrNew(['brand_id' => $brand_id, 'warehouse_id' => $warehouse_id]);
    	$stock->quantity += $quantity;
        $stock->save();
    }
}
