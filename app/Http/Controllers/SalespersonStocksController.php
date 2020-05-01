<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brand;
use App\Salesperson;
use App\SalespersonStock;
use App\AllocationBrand;
use App\Price;
use PDF;

class SalespersonStocksController extends BaseController
{
    protected $mainModel = 'App\SalespersonStock';

    // params needen for index
    protected $searchFields = [];
    protected $indexPaginate = 10;
    protected $indexJoins = [];

    // params needer for show
    protected $showJoins = [];

    // params needed for store/update
    protected $saveFields = [];
    // protected $storeFields = [];
    // protected $updateFields = [];
    protected $defaultNulls = [];
    protected $formRules = [];

    protected $allowDelete = false;
    protected $allowUpdate = false;
    protected $allowStore  = false;
    protected $except = [];

    protected $useTransactions = false;


    public function index(Request $request)
    {
        $model = new Brand;
        $page = $request->page;
        $search = $request->search ?? '';
        
        if (! $request->filters) {
            return [];
        }
        
        $key = array_search('salesperson_id', array_column($request->filters, 'field'));
        if ($key === false) {
            return [];
        }

        $salesperson_id = $request->filters[$key]['value'];

        // filter active
        $key = array_search('active', array_column($request->filters, 'field'));
        if ($key !== false) {
            $active = $request->filters[$key]['value'];
            $model = $model->where('active', $active);
        }

        if ($search) {
            $model = $model->where('name', 'like', '%'.$search.'%');
        }

        $model = $model->orderBy('name', 'asc');
        $model = $model->paginate($this->indexPaginate);

        $model = json_decode(json_encode($model));

        $data = [];
        foreach ($model->data as $item) {
            $stock = SalespersonStock::where('brand_id', $item->id)
                          ->where('salesperson_id', $salesperson_id)
                          ->first();
            $quantity = ($stock) ? $stock->quantity : 0;

            $data[] = [
                'id'     => $item->id,
                'name'   => $item->name,
                'active' => $item->active,
                'stock'  => floatval($quantity),
                'packs_per_box' => $item->packs_per_box
            ];
        }

        $model->data = $data;
        return json_encode($model);
    }

    public function report(Salesperson $salesperson, Request $request)
    {
        $stocks = SalespersonStock::join('brands', 'brand_id', '=', 'brands.id')
                                  ->select('name', 'brand_id', 'packs_per_box', 'cost', 'price', 'quantity')
                                  ->where('salesperson_id', $salesperson->id)
                                  ->where('quantity', '!=', 0)
                                  ->orderBy('name')
                                  ->get();

        // Now it will take price from salesperson or brand
        foreach ($stocks as $key => $item) {
            $stocks[$key]['price'] = Price::getPrice($item->brand_id, $salesperson->id);
        }

        $data = [
            'salesperson' => $salesperson->name,
            'stocks'      => $stocks,
            'sum_boxes'   => 0,
            'sum_packs'   => 0,
            'sum_price'   => 0
        ];

        $pdf = PDF::loadView('reports/salesperson_stocks', $data);
        return $pdf->stream('rpt_existencias_ventas.pdf', ['Attachment' => false]);
    }

    public function kardex(Salesperson $salesperson, Brand $brand, Request $request)
    {
        $details = AllocationBrand::join('allocations as all', 'all.id', '=', 'allocation_brands.allocation_id')
                                ->join('brands as b', 'b.id', '=', 'allocation_brands.brand_id')
                                ->select('all.id AS movID', 'quantity', 'all.rec_date', 'all.type', 'b.packs_per_box', 'all.doc_number', 'all.comments')
                                ->where('b.id', $brand->id)
                                ->where('all.salesperson_id', $salesperson->id)
                                ->where('all.active', true)
                                ->orderBy('all.rec_date')
                                ->get();

        $boxes = $request->boxes ?? 1;

        $data = [
            'salesperson' => $salesperson->name,
            'brand'       => $brand->name,
            'use_boxes'   => $boxes,
            'details'     => $details,
            'balance'     => 0
        ];

        $pdf = PDF::loadView('reports/salesperson_kardex', $data);
        return $pdf->stream('rpt_kardex_ventas.pdf', ['Attachment' => false]);
    }
}
