<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brand;
use App\Stock;
use App\Warehouse;
use App\MovementBrand;
use PDF;

class StocksController extends BaseController
{
    protected $mainModel = 'App\Stock';

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
        
        $key = array_search('warehouse_id', array_column($request->filters, 'field'));
        if ($key === false) {
            return [];
        }

        $warehouse_id = $request->filters[$key]['value'];

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
            $stock = Stock::where('brand_id', $item->id)
                          ->where('warehouse_id', $warehouse_id)
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

    public function report(Warehouse $warehouse, Request $request)
    {
        $stocks = Stock::join('brands', 'brand_id', '=', 'brands.id')
                       ->select('name', 'packs_per_box', 'cost', 'quantity')
                       ->where('warehouse_id', $warehouse->id)
                       ->where('quantity', '!=', 0)
                       ->orderBy('name')
                       ->get();

        $boxes = $request->boxes ?? 1;

        $data = [
            'warehouse' => $warehouse->name,
            'use_boxes' => $boxes,
            'stocks'    => $stocks,
            'total'     => 0
        ];

        $pdf = PDF::loadView('reports/stocks', $data);
        return $pdf->stream('rpt_existencias.pdf', ['Attachment' => false]);
    }

    public function kardex(Warehouse $warehouse, Brand $brand, Request $request)
    {
        $details = MovementBrand::join('movements as mov', 'mov.id', '=', 'movement_brands.movement_id')
                                ->join('brands as b', 'b.id', '=', 'movement_brands.brand_id')
                                ->join('movement_concepts as mc', 'mc.id', '=', 'mov.concept_id')
                                ->select('mov.id AS movID', 'quantity', 'mov.mov_date', 'mov.type', 'b.packs_per_box', 'mc.name', 'mov.comments')
                                ->where('b.id', $brand->id)
                                ->where('mov.warehouse_id', $warehouse->id)
                                ->where('mov.active', true)
                                ->orderBy('mov.mov_date')
                                ->get();

        $boxes = $request->boxes ?? 1;

        $data = [
            'warehouse' => $warehouse->name,
            'brand'     => $brand->name,
            'use_boxes' => $boxes,
            'details'   => $details,
            'balance'   => 0
        ];

        $pdf = PDF::loadView('reports/kardex', $data);
        return $pdf->stream('rpt_kardex.pdf', ['Attachment' => false]);
    }
}
