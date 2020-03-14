<?php

namespace App\Libraries;

use App\Brand;
use App\SalespersonStock;
use App\Allocation;

class Amounts
{
	protected $brandID, $salespersonID;
	private $amounts;
	private $currMov;


	public function __construct()
	{
		$this->amounts = [
			'cost'  => 0,
			'price' => 0
		];

		$this->currMov = false;
	}

	public function getInventory($warehouse_id = false)
	{
		// code
	}

	public function getDistributionsCost($salesperson_id, $brand_id, $quantity = false)
	{
		$amounts = $this->getDistributions($salesperson_id, $brand_id, $quantity);
		return $amounts['cost'];
	}

	public function getDistributionsPrice($salesperson_id, $brand_id, $quantity = false)
	{
		$amounts = $this->getDistributions($salesperson_id, $brand_id, $quantity);
		return $amounts['price'];;
	}

	public function getDistributions($salesperson_id, $brand_id, $quantity = false)
	{
		$this->brandID = $brand_id;
		$this->salespersonID = $salesperson_id;
		$this->currMov = false;
		$this->amounts = ['cost'  => 0, 'price' => 0];


		if ($quantity === false) {
			$stock = SalespersonStock::where('brand_id', $brand_id)
			                         ->where('salesperson_id', $salesperson_id)
			                         ->first();
			
			if ($stock) {
				if ($stock->quantity > 0) {
					$this->getDistributionsAmounts($stock->quantity);
				} else {
					$this->calculateUsingBrandAmounts($stock->quantity);
				}
			}
		} else {
			$this->getDistributionsAmounts($quantity);
		}

		return $this->amounts;
	}


	private function calculateUsingBrandAmounts($quantity)
	{
		if ($brand = Brand::find($this->brandID)) {
			$this->amounts = [
				'cost'  => $quantity * $brand->cost,
				'price' => $quantity * $brand->price,
			];
		}
	}

	private function getDistributionsAmounts($quantity)
	{
		do {
			$quantity = $this->processNextMovement($quantity);
		} while ($quantity > 0);
	}

	private function processNextMovement($quantity)
	{
		$model = Allocation::with('details')
		                   ->where('salesperson_id', $this->salespersonID)
		                   ->where('type', 'E')
		                   ->where('active', true);

		if ($this->currMov) {
			$model->where('id', '<', $this->currMov);
		}
		
		$record = $model->orderBy('rec_date', 'desc')->first();

		if ($record) {
			$this->currMov = $record->id;

			foreach ($record->details as $det) {
				if ($det->brand_id == $this->brandID) {
					if ($det->quantity <= $quantity) {
						$this->amounts['cost']  += ($det->quantity * $det->unit_cost);
						$this->amounts['price'] += ($det->quantity * $det->unit_price);
						$quantity -= $det->quantity;
					} else {
						$this->amounts['cost']  += ($quantity * $det->unit_cost);
						$this->amounts['price'] += ($quantity * $det->unit_price);
						$quantity = 0;
					}
				}
			}
		} else {
			$quantity = 0;
		}

		return $quantity;
	}
}