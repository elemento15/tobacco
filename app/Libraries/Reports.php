<?php

namespace App\Libraries;

use App\Salesperson;
use App\Allocation;

class Reports
{
	public function __construct()
	{
		//
	}

	public function getDateRangeSales($params)
	{
		$data = [];
		$sum_price = 0;

		$salespersons = Salesperson::where('active', true)
                                   ->orderBy('name')
		                           ->get();

		foreach ($salespersons as $sp) {
			$allocations =  Allocation::with('amount')
			                          ->whereBetween('rec_date', [$params['ini_date'], $params['end_date']])
			                          ->where('type', 'L')
			                          ->where('salesperson_id', $sp->id)
			                          ->where('active', true)
			                          ->get();

			$cost = 0;
			$price = 0;
			$items = 0;

			foreach ($allocations as $alloc) {
				$cost += $alloc->amount->cost;
				$price += $alloc->amount->price;
				// sum of boxes
				foreach ($alloc->details as $det) {
					$items += $det->quantity / $det->brand->packs_per_box;
				}
			}

			$data[] = [
				'name' => $sp->name,
				'cost' => $cost,
				'price' => $price,
				'items' => $items,
			];

			$sum_price += $price; // get sum of prices to calculate percents
		}

		// calculate percents
		foreach ($data as $key => $item) {
			$data[$key]['percent'] = ($item['price']) ? $item['price'] / $sum_price : 0;
		}

		return $data;
	}
}