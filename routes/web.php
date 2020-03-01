<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (! Auth::user()) {
		return view('auth.login');
	}
});

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
	Route::get('/', 'HomeController@index')->name('home');

	//Brands
	Route::resource('brands', 'BrandsController')->only(['index','show']);

	// Salespersons
	Route::resource('salespersons', 'SalespersonsController')->only(['index','show']);


	// Only for: SYS, ADM, INV, ALM
	Route::group(['middleware' => ['role:SYS,ADM,INV,ALM']], function () {
		// Warehouses
		Route::resource('warehouses', 'WarehousesController')->only(['index','show']);

		// Movement Concepts
		Route::resource('concepts', 'MovementConceptsController')->only(['index','show']);
		
		// Movements
		Route::resource('movements', 'MovementsController')->only(['index','show']);

		// Stocks
		Route::resource('stocks', 'StocksController')->only(['index']);
		Route::get('stocks/report/{warehouse}', 'StocksController@report');
		Route::get('stocks/kardex/{warehouse}/{brand}', 'StocksController@kardex');

		// Allocations
		Route::resource('allocations', 'AllocationsController');
		Route::post('allocations/{id}/cancel', 'AllocationsController@cancel');


		// Only for: SYS, ADM, INV
		Route::group(['middleware' => ['role:SYS,ADM,INV']], function () {
			// Warehouses
			Route::resource('warehouses', 'WarehousesController')->only(['store','update','destroy']);
			Route::post('warehouses/{id}/activate', 'WarehousesController@activate');
			Route::post('warehouses/{id}/deactivate', 'WarehousesController@deactivate');

			// Movements
			Route::resource('movements', 'MovementsController')->only(['store']);
			Route::post('movements/{id}/cancel', 'MovementsController@cancel');

			// Only for: SYS, ADM
			Route::group(['middleware' => ['role:SYS,ADM']], function () {
				// Roles
				Route::resource('roles', 'RolesController')->only(['index']);
				
				// Users
				Route::resource('users', 'UsersController')->except(['create','edit']);
				Route::post('users/{id}/activate', 'UsersController@activate');
				Route::post('users/{id}/deactivate', 'UsersController@deactivate');

				// Brands
				Route::resource('brands', 'BrandsController')->only(['store','update','destroy']);
				Route::post('brands/{id}/activate', 'BrandsController@activate');
				Route::post('brands/{id}/deactivate', 'BrandsController@deactivate');

				// Salespersons
				Route::resource('salespersons', 'SalespersonsController')->only(['store','update','destroy']);
				Route::post('salespersons/{id}/activate', 'SalespersonsController@activate');
				Route::post('salespersons/{id}/deactivate', 'SalespersonsController@deactivate');
				Route::get('salespersons/{id}/prices', 'SalespersonsController@getPrices');
				Route::post('salespersons/{id}/prices', 'SalespersonsController@savePrices');

				// Only for: SYS
				Route::group(['middleware' => ['role:SYS']], function () {
					// ---
				});
			});
		});
	});
});