<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brand;
use App\Stock;
use App\Warehouse;
use App\MovementBrand;
use App\Configuration;
use Carbon\Carbon;
use PDF;
use DB;

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

    protected $kardexDate = false;


    public function __construct()
    {
        if ($days = Configuration::getKardexDays()) {
            $dt = Carbon::now();
            $this->kardexDate = $dt->subDays($days)->format('Y-m-d');
        }
    }

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
            $model = $model->where('brands.active', $active);
        }

        // filter brand_type
        $key = array_search('brand_type_id', array_column($request->filters, 'field'));
        if ($key !== false) {
            $type = $request->filters[$key]['value'];
            $model = $model->where('brand_type_id', $type);
        }

        if ($search) {
            $model = $model->where('brands.name', 'like', '%'.$search.'%');
        }

        $model = $model->join('brand_types', 'brand_types.id', '=', 'brands.brand_type_id')
                       ->orderBy('brands.name', 'asc')
                       ->select('brands.id','brands.name','brands.active','packs_per_box','brand_types.name AS type')
                       ->paginate($this->indexPaginate);

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
                'type'   => $item->type,
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
        $stocks = Stock::join('brands AS b', 'stocks.brand_id', '=', 'b.id')
                       ->join('brand_types AS bt', 'bt.id', '=', 'b.brand_type_id')
                       ->select('b.name', 'bt.name AS type', 'b.packs_per_box', 'b.cost', 'stocks.quantity')
                       ->where('stocks.warehouse_id', $warehouse->id)
                       ->where('stocks.quantity', '!=', 0);

        if ($request->type) {
            $stocks = $stocks->where('b.brand_type_id', $request->type);
        }

        $stocks = $stocks->orderBy('name')
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
        $balance = 0;
        $details = MovementBrand::join('movements as mov', 'mov.id', '=', 'movement_brands.movement_id')
                                ->join('brands as b', 'b.id', '=', 'movement_brands.brand_id')
                                ->join('movement_concepts as mc', 'mc.id', '=', 'mov.concept_id')
                                ->select('mov.id AS movID', 'quantity', 'mov.mov_date', 'mov.type', 'b.packs_per_box', 'mc.name', 'mov.comments')
                                ->where('b.id', $brand->id)
                                ->where('mov.warehouse_id', $warehouse->id)
                                ->where('mov.active', true);

        if ($this->kardexDate) {
            $details = $details->where('mov.mov_date', '>=', $this->kardexDate); // filter by date
            $balance = $this->getInitialBalance($brand, $warehouse); // get the balance for previous dates
        }
        
        $details = $details->orderBy('mov.mov_date')->get();

        $boxes = $request->boxes ?? 1;

        $data = [
            'warehouse'     => $warehouse->name,
            'brand'         => $brand->name,
            'use_boxes'     => $boxes,
            'packs_per_box' => $brand->packs_per_box,
            'details'       => $details,
            'balance'       => $balance,
            'from_date'     => ($this->kardexDate) ? 'Desde: '. $this->kardexDate : '',
        ];

        $pdf = PDF::loadView('reports/kardex', $data);
        return $pdf->stream('rpt_kardex.pdf', ['Attachment' => false]);
    }


    private function getInitialBalance($brand, $warehouse)
    {
        $select = 'SUM(IF(mov.`type` = "E", movement_brands.quantity, movement_brands.quantity * -1)) AS balance';

        $rec = MovementBrand::select(DB::raw($select))
                            ->join('movements as mov', 'mov.id', '=', 'movement_brands.movement_id')
                            ->where('mov.mov_date', '<', $this->kardexDate)
                            ->where('movement_brands.brand_id', $brand->id)
                            ->where('mov.warehouse_id', $warehouse->id)
                            ->where('mov.active', true)
                            ->first();

        return $rec->balance ?? 0;
    }
}
