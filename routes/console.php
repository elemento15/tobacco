<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('calc-amounts', function() {
	$a = new App\Libraries\Amounts();

	$amounts = $a->getDistributions(1, 2);

	\Log::info('Cost: '. $amounts['cost']);
	\Log::info('Price: '. $amounts['price']);
});

Artisan::command('normalize-brand-types', function () {
	if (! App\BrandType::count()) {
		// create 2 brand_types and relate with warehouse
		$types = [
			['code' => 'CIGAR', 'name' => 'CIGARROS', 'warehouse_id' => 2],
			['code' => 'DULCE', 'name' => 'DULCERIA', 'warehouse_id' => 5],
		];

		foreach ($types as $type) {
			App\BrandType::create($type);
		}
		$this->comment('- Tipos de marca creados (CIGARROS, DULCERIA)');

		$type = App\BrandType::where('code', 'CIGAR')->first();

		// set "CIGAR" brand type to all existing brands
		$brands = App\Brand::all();
		foreach ($brands as $brand) {
			$brand->brand_type_id = $type->id;
			$brand->save();
		}
		$this->comment('- Asignado tipo de marca a todas las marcas');

		// set "CIGAR" brand type to all existing allocations
		$allocations = App\Allocation::all();
		foreach ($allocations as $item) {
			$item->brand_type_id = $type->id;
			$item->save();
		}
		$this->comment('- Asignado tipo de marca a todas las distribuciones');
	} else {
		$this->error('Tipos de Marca ya existentes.');
	}
})->describe('Normalize information to use  brand types');