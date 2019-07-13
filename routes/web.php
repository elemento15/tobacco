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

	Route::resource('brands', 'BrandsController');
	Route::post('brands/{id}/activate', 'BrandsController@activate');
	Route::post('brands/{id}/deactivate', 'BrandsController@deactivate');

	Route::resource('salespersons', 'SalespersonsController');
	Route::post('salespersons/{id}/activate', 'SalespersonsController@activate');
	Route::post('salespersons/{id}/deactivate', 'SalespersonsController@deactivate');
});