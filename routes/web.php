<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\BrandTypesController;
use App\Http\Controllers\SalespersonsController;
use App\Http\Controllers\AllocationsController;
use App\Http\Controllers\SalespersonStocksController;
use App\Http\Controllers\ConfigurationsController;
use App\Http\Controllers\ChartsController;
use App\Http\Controllers\WarehousesController;
use App\Http\Controllers\MovementConceptsController;
use App\Http\Controllers\MovementsController;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;

Route::get('/', function () {
    if (! Auth::user()) {
        return view('auth.login');
    }
});

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    //Brands
    Route::resource('brands', BrandsController::class)->only(['index','show']);

    //Brand Types
    Route::resource('brand_types', BrandTypesController::class)->only(['index','show']);

    // Salespersons
    Route::resource('salespersons', SalespersonsController::class)->only(['index','show']);

    // Allocations
    Route::resource('allocations', AllocationsController::class);
    Route::post('allocations/getDetailAmounts', [AllocationsController::class, 'getDetailAmounts']);

    // Stocks - Salesperson
    Route::resource('salesperson_stocks', SalespersonStocksController::class)->only(['index']);
    Route::get('salesperson_stocks/report/{salesperson}', [SalespersonStocksController::class, 'report']);
    Route::get('salesperson_stocks/kardex/{salesperson}/{brand}', [SalespersonStocksController::class, 'kardex']);

    // Configurations
    Route::get('configurations', [ConfigurationsController::class, 'get']);


    // Only for: SYS, ADM
    Route::group(['middleware' => ['role:SYS,ADM']], function () {
        // Charts
        Route::get('charts/sales', [ChartsController::class, 'sales']);
        Route::get('charts/salesperson', [ChartsController::class, 'salesperson']);
        Route::get('charts/weekly', [ChartsController::class, 'weekly']);
    });

    // Only for: SYS, ADM, AUX
    Route::group(['middleware' => ['role:SYS,ADM,AUX']], function () {
        // Reports
        Route::get('reports', [HomeController::class, 'reports']);
        Route::get('download', [HomeController::class, 'download']);
    });


    // Only for: SYS, ADM, INV, ALM
    Route::group(['middleware' => ['role:SYS,ADM,INV,ALM']], function () {
        // Warehouses
        Route::resource('warehouses', WarehousesController::class)->only(['index','show']);

        // Movement Concepts
        Route::resource('concepts', MovementConceptsController::class)->only(['index','show']);

        // Movements
        Route::resource('movements', MovementsController::class)->only(['index','show']);

        // Stocks - Warehouse
        Route::resource('stocks', StocksController::class)->only(['index']);
        Route::get('stocks/report/{warehouse}', [StocksController::class, 'report']);
        Route::get('stocks/kardex/{warehouse}/{brand}', [StocksController::class, 'kardex']);


        // Only for: SYS, ADM, INV
        Route::group(['middleware' => ['role:SYS,ADM,INV']], function () {
            // Warehouses
            Route::resource('warehouses', WarehousesController::class)->only(['store','update','destroy']);
            Route::post('warehouses/{id}/activate', [WarehousesController::class, 'activate']);
            Route::post('warehouses/{id}/deactivate', [WarehousesController::class, 'deactivate']);

            // Movements
            Route::resource('movements', MovementsController::class)->only(['store']);
            Route::post('movements/{id}/cancel', [MovementsController::class, 'cancel']);


            // Only for: SYS, ADM
            Route::group(['middleware' => ['role:SYS,ADM']], function () {
                // Roles
                Route::resource('roles', RolesController::class)->only(['index']);

                // Users
                Route::resource('users', UsersController::class)->except(['create','edit']);
                Route::post('users/{id}/activate', [UsersController::class, 'activate']);
                Route::post('users/{id}/deactivate', [UsersController::class, 'deactivate']);

                // Brands
                Route::resource('brands', BrandsController::class)->only(['store','update','destroy']);
                Route::post('brands/{id}/activate', [BrandsController::class, 'activate']);
                Route::post('brands/{id}/deactivate', [BrandsController::class, 'deactivate']);

                // Salespersons
                Route::resource('salespersons', SalespersonsController::class)->only(['store','update','destroy']);
                Route::post('salespersons/{id}/activate', [SalespersonsController::class, 'activate']);
                Route::post('salespersons/{id}/deactivate', [SalespersonsController::class, 'deactivate']);
                Route::get('salespersons/{id}/prices', [SalespersonsController::class, 'getPrices']);
                Route::post('salespersons/{id}/prices', [SalespersonsController::class, 'savePrices']);

                // Allocations
                Route::post('allocations/{id}/cancel', [AllocationsController::class, 'cancel']);

                // Configurations
                Route::post('configurations', [ConfigurationsController::class, 'save']);


                // Only for: SYS
                Route::group(['middleware' => ['role:SYS']], function () {
                    // ---
                });
            });
        });
    });
});
