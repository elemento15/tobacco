<?php

namespace App;

use App\Brand;
use App\Salesperson;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
	protected $fillable = ['brand_id', 'salesperson_id', 'price'];

    public function brand()
    {
        return $this->belongsTo('App\Brand');
    }

    public function salesperson()
    {
        return $this->belongsTo('App\Salesperson');
    }


    public static function getPrice($brand_id, $salesperson_id)
    {
    	$price = self::where('brand_id', $brand_id)
    	             ->where('salesperson_id', $salesperson_id)
    	             ->first();

    	if (! $price) {
    		$brand = Brand::find($brand_id);
    		return $brand->price;
    	} else {
    		return $price->price;
    	}
    }
}
