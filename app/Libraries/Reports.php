<?php

namespace App\Libraries;

use App\Salesperson;
use App\Allocation;
use App\SalespersonStock;
use App\Price;
use App\MovementCancellation;
use App\Movement;
use App\AllocationCancellation;
use Carbon\Carbon;
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

		$salespersons = Salesperson::orderBy('name')->get();

		foreach ($salespersons as $sp) {
			$allocations =  Allocation::with('amount')
			                          ->whereBetween('rec_date', [$params['ini_date'], $params['end_date']])
			                          ->where('type', 'L')
			                          ->where('salesperson_id', $sp->id)
			                          ->where('active', true);

			if ($params['type']) {
				$allocations = $allocations->where('brand_type_id', $params['type']);
			}
			
			$allocations = $allocations->get();

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

			if ($params['omit_zero'] && !$items || !$sp->active && !$items) {
				continue;
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

	public function getSalesPersonSummary($params)
	{
		$data = [];
		$type = $params['type'];
		//$oAmount = new Amounts();

		$salespersons = Salesperson::orderBy('name')->get();

		foreach ($salespersons as $sp) {
			$stocks = SalespersonStock::with('brand')
			                          ->where('salesperson_id', $sp->id)
			                          ->where('quantity', '!=', 0);

			if ($type) {
				$stocks = $stocks->whereHas('brand', function ($q) use ($type) {
					$q->where('brand_type_id', $type);
				});
			}
			
			$stocks = $stocks->get();

			$packs  = 0;
			$boxes  = 0;
			$amount = 0;

			foreach ($stocks as $item) {
				$packs  += $item->quantity;
				$boxes  += $item->quantity / $item->brand->packs_per_box;
				//$amount += $oAmount->getDistributionsPrice($sp->id, $item->brand_id);
				$amount += $item->quantity * Price::getPrice($item->brand->id, $sp->id);
			}

			if (($params['omit_zero'] && !$packs) || (!$sp->active && !$packs)) {
				continue;
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

	public function getCancellations($params)
	{
		$data = [];

		if ($params['doc_type'] == 'M') {
			$data = $this->getMovementsCancellations($params['ini_date']);
		} else {
			$data = $this->getDistributionsCancellations($params['ini_date']);
		}

		return $data;
	}


	private function getMovementsCancellations($date)
	{
		$data = collect();
		$docs = MovementCancellation::with('user:id,name')
		                            ->where('cancel_date', '>=', $date)
		                            ->select('id','cancel_date','user_id')
		                            ->get();

		foreach ($docs as $item) {
			$mov = Movement::with('warehouse:id,name', 'concept:id,name', 'user:id,name')
			               ->where('cancellation_id', $item->id)
			               ->select('id','mov_date','type','warehouse_id','concept_id','user_id')
			               ->first();

			$mov_date = Carbon::create($mov->mov_date);
			$diff_days = $mov_date->diffInDays($item->cancel_date);

			$data->push([
				'cancel_id' => $item->id,
				'cancel_date' => $item->cancel_date,
				'cancel_user_id' => $item->user->id,
				'cancel_user_name' => $item->user->name,
				'mov_id'   => $mov->id,
				'mov_date' => $mov->mov_date,
				'mov_type' => $mov->type,
				'mov_warehouse_id' => $mov->warehouse->id,
				'mov_warehouse_name' => $mov->warehouse->name,
				'mov_concept_id' => $mov->concept->id,
				'mov_concept_name' => $mov->concept->name,
				'mov_user_id' => $mov->user->id,
				'mov_user_name' => $mov->user->name,
				'days' => $diff_days
			]);
		}

		return $data;
	}

	private function getDistributionsCancellations($date)
	{
		$data = collect();
		$docs = AllocationCancellation::with('user:id,name')
		                              ->where('cancel_date', '>=', $date)
		                              ->select('id','cancel_date','user_id')
		                              ->get();

		foreach ($docs as $item) {
			$mov = Allocation::with('user:id,name', 'salesperson:id,name')
			                 ->where('cancellation_id', $item->id)
			                 ->select('id','rec_date','type','doc_number','salesperson_id','user_id')
			                 ->first();

			$mov_date = Carbon::create($mov->rec_date);
			$diff_days = $mov_date->diffInDays($item->cancel_date);

			$data->push([
				'cancel_id' => $item->id,
				'cancel_date' => $item->cancel_date,
				'cancel_user_id' => $item->user->id,
				'cancel_user_name' => $item->user->name,
				'mov_id'   => $mov->id,
				'mov_date' => $mov->rec_date,
				'mov_type' => $mov->type,
				'doc_number' => $mov->doc_number,
				'mov_salesperson_id' => $mov->salesperson->id,
				'mov_salesperson_name' => $mov->salesperson->name,
				'mov_user_id' => $mov->user->id,
				'mov_user_name' => $mov->user->name,
				'days' => $diff_days
			]);
		}

		return $data;
	}
}