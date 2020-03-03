<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalespersonStock extends Model
{
    protected $fillable = ['brand_id', 'salesperson_id', 'quantity'];

    public $timestamps = false;

    public function brand()
    {
        return $this->belongsTo('App\Brand');
    }

    public function salesperson()
    {
        return $this->belongsTo('App\Salesperon');
    }

    public static function addStock($brand_id, $salesperson_id, $quantity = 0)
    {
        self::updateStock($brand_id, $salesperson_id, $quantity);
    }


    public static function substractStock($brand_id, $salesperson_id, $quantity = 0)
    {
    	self::updateStock($brand_id, $salesperson_id, $quantity * -1);
    }


    private static function updateStock($brand_id, $salesperson_id, $quantity)
    {
    	$stock = self::firstOrNew(['brand_id' => $brand_id, 'salesperson_id' => $salesperson_id]);
    	$stock->quantity += $quantity;
        $stock->save();
    }
}
