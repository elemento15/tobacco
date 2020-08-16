<?php

namespace App\Http\Controllers;

use App\Salesperson;
use App\Price;
use Illuminate\Http\Request;
use Response;

class SalespersonsController extends BaseController
{
    protected $mainModel = 'App\Salesperson';

    // params needen for index
    protected $searchFields = ['name','mobile'];
    protected $indexPaginate = 10;
    protected $indexJoins = [];

    // params needer for show
    protected $showJoins = [];

    // params needed for store/update
    protected $saveFields = ['name','mobile'];
    // protected $storeFields = [];
    // protected $updateFields = [];
    protected $defaultNulls = ['mobile'];
    protected $formRules = [
        'name' => 'required|min:4',
    ];

    protected $allowDelete = true;
    protected $allowUpdate = true;
    protected $allowStore  = true;
    protected $except = [];

    protected $useTransactions = false;


    public function getPrices($id)
    {
        $salesperson = Salesperson::findOrFail($id);
        return $salesperson->prices;
    }

    public function savePrices($id, Request $request)
    {
        $salesperson = Salesperson::findOrFail($id);
        
        foreach ($request->data as $item) {
            if ($item['price'] != $item['brand_price']) {
                Price::updateOrCreate(
                    ['brand_id' => $item['id'], 'salesperson_id' => $salesperson->id],
                    ['price' => $item['price']]
                );
            } else {
                $price = Price::where('brand_id', $item['id'])
                              ->where('salesperson_id', $salesperson->id)
                              ->first();

                if ($price) {
                    $price->delete();
                }
            }
        }

        return Response::json(array('success' => true));
    }
}
