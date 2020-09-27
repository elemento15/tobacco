<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brand;
use App\Salesperson;
use App\SalespersonStock;
use App\AllocationBrand;
use App\Price;
use App\Configuration;
use Carbon\Carbon;
use PDF;
use DB;

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

    protected $kardexDate = false;


    public function __construct()
    {
        if ($days = Configuration::getKardexDays('S')) {
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
        
        $key = array_search('salesperson_id', array_column($request->filters, 'field'));
        if ($key === false) {
            return [];
        }

        $salesperson_id = $request->filters[$key]['value'];

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
            $stock = SalespersonStock::where('brand_id', $item->id)
                          ->where('salesperson_id', $salesperson_id)
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

    public function report(Salesperson $salesperson, Request $request)
    {
        $stocks = SalespersonStock::join('brands AS b', 'salesperson_stocks.brand_id', '=', 'b.id')
                                  ->join('brand_types AS bt', 'bt.id', '=', 'b.brand_type_id')
                                  ->select('b.name', 'bt.name AS type', 'salesperson_stocks.brand_id', 'b.packs_per_box', 
                                           'b.cost', 'b.price', 'salesperson_stocks.quantity')
                                  ->where('salesperson_stocks.salesperson_id', $salesperson->id)
                                  ->where('salesperson_stocks.quantity', '!=', 0);

        if ($request->type) {
            $stocks = $stocks->where('b.brand_type_id', $request->type);
        }
        
        $stocks = $stocks->orderBy('name')
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
        $balance = 0;
        $details = AllocationBrand::join('allocations as all', 'all.id', '=', 'allocation_brands.allocation_id')
                                ->join('brands as b', 'b.id', '=', 'allocation_brands.brand_id')
                                ->select('all.id AS movID', 'quantity', 'all.rec_date', 'all.type', 'b.packs_per_box', 'all.doc_number', 'all.comments')
                                ->where('b.id', $brand->id)
                                ->where('all.salesperson_id', $salesperson->id)
                                ->where('all.active', true);

        if ($this->kardexDate) {
            $details = $details->where('all.rec_date', '>=', $this->kardexDate); // filter by date
            $balance = $this->getInitialBalance($brand, $salesperson); // get the balance for previous dates
        }

        $details = $details->orderBy('all.rec_date')->get();

        $boxes = $request->boxes ?? 1;

        $data = [
            'salesperson'   => $salesperson->name,
            'brand'         => $brand->name,
            'use_boxes'     => $boxes,
            'packs_per_box' => $brand->packs_per_box,
            'details'       => $details,
            'balance'       => $balance,
            'from_date'     => ($this->kardexDate) ? 'Desde: '. $this->kardexDate : '',
        ];

        $pdf = PDF::loadView('reports/salesperson_kardex', $data);
        return $pdf->stream('rpt_kardex_ventas.pdf', ['Attachment' => false]);
    }


    private function getInitialBalance($brand, $salesperson)
    {
        $select = 'SUM(IF(a.`type` = "L", allocation_brands.quantity * -1, allocation_brands.quantity)) AS balance';

        $rec = AllocationBrand::select(DB::raw($select))
                              ->join('allocations as a', 'a.id', '=', 'allocation_brands.allocation_id')
                              ->where('a.rec_date', '<', $this->kardexDate)
                              ->where('allocation_brands.brand_id', $brand->id)
                              ->where('a.salesperson_id', $salesperson->id)
                              ->where('a.active', true)
                              ->first();

        return $rec->balance ?? 0;
    }
}
