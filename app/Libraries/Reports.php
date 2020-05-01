<?php

namespace App\Libraries;

use App\Salesperson;
use App\Allocation;
use App\SalespersonStock;
use App\Price;
//use App\Libraries\Amounts;

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
			$packs = 0;

			foreach ($allocations as $alloc) {
				$cost += $alloc->amount->cost;
				$price += $alloc->amount->price;
				// sum of boxes and packs
				foreach ($alloc->details as $det) {
					$items += $det->quantity / $det->brand->packs_per_box;
					$packs += $det->quantity;
				}
			}

			$data[] = [
				'name' => $sp->name,
				'cost' => $cost,
				'price' => $price,
				'items' => $items,
				'packs' => $packs,
			];

			$sum_price += $price; // get sum of prices to calculate percents
		}

		// calculate percents
		foreach ($data as $key => $item) {
			$data[$key]['percent'] = ($item['price']) ? $item['price'] / $sum_price : 0;
		}

		return $data;
	}

	public function getSalesPersonSummary()
	{
		$data = [];
		//$oAmount = new Amounts();

		$salespersons = Salesperson::where('active', true)
                                   ->orderBy('name')
		                           ->get();

		foreach ($salespersons as $sp) {
			$stocks = SalespersonStock::with('brand')
			                          ->where('salesperson_id', $sp->id)
			                          ->where('quantity', '!=', 0)
			                          ->get();

			$packs  = 0;
			$boxes  = 0;
			$amount = 0;

			foreach ($stocks as $item) {
				$packs  += $item->quantity;
				$boxes  += $item->quantity / $item->brand->packs_per_box;
				//$amount += $oAmount->getDistributionsPrice($sp->id, $item->brand_id);
				$amount += $item->quantity * Price::getPrice($item->brand->id, $sp->id);
			}

			$data[] = [
				'name'   => $sp->name,
				'packs'  => $packs,
				'boxes'  => $boxes,
				'amount' => $amount,
				'has_prices' => $sp->prices->count()
			];
		}

		return $data;
	}
}