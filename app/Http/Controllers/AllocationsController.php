<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuration;
use App\Brand;
use App\AllocationBrand;
use App\Movement;
use App\MovementConcept;
use App\MovementBrand;
use App\MovementCancellation;
use App\Stock;
use App\Price;
use App\AllocationCancellation;
use App\SalespersonStock;
use App\AllocationAmount;
use App\BrandType;
use Response;
use Illuminate\Support\Facades\DB;

class AllocationsController extends BaseController
{
    protected $mainModel = 'App\Allocation';

    // params needen for index
    protected $searchFields = ['id','doc_number'];
    protected $indexPaginate = 10;
    protected $indexJoins = ['warehouse', 'salesperson', 'amount', 'brand_type'];

    // params needer for show
    protected $showJoins = ['warehouse', 'salesperson', 'brand_type', 'details.brand', 'user', 'cancellation.user'];

    // params needed for store/update
    protected $saveFields = ['rec_date','salesperson_id','warehouse_id','type','brand_type_id','user_id','doc_number','comments'];
    //protected $storeFields = [];
    //protected $updateFields = [];
    protected $defaultNulls = ['warehouse_id','user_id','cancel_user_id','cancel_date','comments'];
    protected $formRules = [
        'salesperson_id' => 'required',
        'type' => 'required',
        'brand_type_id' => 'required'
    ];

    protected $allowDelete = false;
    protected $allowUpdate = false;
    protected $allowStore  = true;
    protected $except = [];

    protected $useTransactions = true;


    public function cancel($id, Request $request)
    {
        $mainModel = $this->mainModel;
        $record = $mainModel::find($id);

        if (! $record) {
            return Response::json(array('msg' => 'Registro no encontrado'), 500);
        }

        if (! $record->active) {
            return Response::json(array('msg' => 'No puede cancelar un registro cancelado'), 500);
        }

        if (empty($request->comments) ||  strlen($request->comments) < 5) {
            return Response::json(array('msg' => 'Comentario inválido ó muy corto'), 500);
        }
        
        DB::beginTransaction();

        $cancellation = $this->generateCancellation($request->comments);

        $record->active = 0;
        $record->cancellation_id = $cancellation->id;
        
        if ($record->type == 'E' || $record->type == 'D') {
            $this->cancelMovement($record->movement_id, $record->type);
        }

        if ($record->save()) {
            // update "salesperson_stock"
            foreach ($record->details as $item) {
                if ($record->type == 'E') { // ENTREGA
                    SalespersonStock::substractStock($item->brand_id, $record->salesperson_id, $item['quantity']);
                } else {
                    SalespersonStock::addStock($item->brand_id, $record->salesperson_id, $item['quantity']);
                }
            }

            DB::commit();
            return Response::json($record);
        } else {
            DB::rollback();
            return Response::json(array('msg' => 'Error al cancelar'), 500);
        }
    }

    public function getDetailAmounts(Request $request)
    {
        $brand = Brand::find($request->brand_id);
        
        return Response::json([
            'cost'  => $brand->cost * $request->quantity, 
            'price' => Price::getPrice($brand->id, $request->salesperson_id) * $request->quantity
        ]);
    }


    protected function beforeStore()
    {
        $req = $this->request;

        if (! in_array($req->type, ['E', 'L', 'D'])) {
            $this->msgError = 'Tipo inválido';
            return false;
        }

        if (! $req->salesperson_id) {
            $this->msgError = 'Vendedor inválido';
            return false;
        }

        if (! $req->brand_type_id) {
            $this->msgError = 'Tipo de marca inválida';
            return false;
        }

        if (gettype($req->details) != 'array' || count($req->details) == 0) {
            $this->msgError = 'Agregue los detalles del registro';
            return false;
        }

        // validate quantities in details
        foreach ($req->details as $item) {
            if ($item['quantity'] <= 0) {
                $this->msgError = 'Cantidad inválida en detalles ('. $item['quantity'] .')';
                return false;
            }
        }

        // validate all brands belong to a single brand type
        foreach ($req->details as $item) {
            $brand = Brand::find($item['brandID']);
            if ($brand->brand_type_id != $req->brand_type_id) {
                $this->msgError = 'Los marca '. $brand->name .' no corresponse con el tipo seleccionado';
                return false;
            }
        }

        // validations for roles 
        // (review home.blade file to see permissions)
        $role = session('roleCode');
        
        if ($req->type == 'E' && !in_array($role, ['SYS','ADM','INV','AUX'])) {
            $this->msgError = 'Acceso Restringido a Entregas';
            return false;
        }

        if ($req->type == 'L' && !in_array($role, ['SYS','ADM','INV','AUX'])) {
            $this->msgError = 'Acceso Restringido a Liquidaciones';
            return false;
        }

        if ($req->type == 'D' && !in_array($role, ['SYS','ADM','INV'])) {
            $this->msgError = 'Acceso Restringido a Devoluciones';
            return false;
        }


        // get allocation warehouse from brand_type
        $brand_type = BrandType::find($req->brand_type_id);

        $this->request['rec_date'] = date('Y-m-d H:i:s');
        $this->request['salesperson_id'] = $req->salesperson_id;
        $this->request['warehouse_id'] = $brand_type->warehouse_id;
        $this->request['type'] = $req->type;
        $this->request['brand_type_id'] = $req->brand_type_id;
        $this->request['user_id'] = session('userID');
        $this->request['doc_number'] = $req->doc_number;
        $this->request['comments'] = $req->comments;
        $this->request['details'] = $req->details;

        return true;
    }

    protected function afterStore()
    {
        $data = $this->request;
        $details = $data['details'];
        $warehouse_id = $data['warehouse_id'];
        $salesperson_id = $data['salesperson_id'];
        $movement = false;

        $sum_cost = 0;
        $sum_price = 0;

        foreach ($details as $key => $item) {
            // get brand
            $brand = Brand::find($item['brand']['id']);

            $det = new AllocationBrand;
            $det->brand_id = $item['brand']['id'];
            $det->quantity = $item['quantity'];
            $det->unit_cost = $brand->cost;
            $det->unit_price = Price::getPrice($item['brand']['id'], $salesperson_id);

            $det->total_cost  = $det->unit_cost  * $det->quantity;
            $det->total_price = $det->unit_price * $det->quantity;

            if ($data['type'] == 'L') {
                $sum_cost += $det->total_cost;
                $sum_price += $det->total_price;
            }


            $this->savedRecord->details()->save($det);

            // updates "salesperson_stocks"
            if ($data['type'] == 'E') { // ENTREGA
                SalespersonStock::addStock($det->brand->id, $salesperson_id, $item['quantity']);
            } else { // LIQUIDACION or DEVOLUCION
                SalespersonStock::substractStock($det->brand->id, $salesperson_id, $item['quantity']);
            }
        }

        // create movement
        if ($this->request['type'] == 'E') { // Entrega
            $concept  = MovementConcept::getByCode('ENTREGA');
            $movement = $this->generateMovement($this->request, 'S', $concept, 'E'); // SALIDA
        } elseif ($this->request['type'] == 'D') { // Devolucion
            $concept  = MovementConcept::getByCode('DEVOLUC');
            $movement = $this->generateMovement($this->request, 'E', $concept, 'D'); // ENTRADA
        
        } else {
            // save amounts for "Liquidaciones"
            $amount = new AllocationAmount;
            $amount->cost = $sum_cost;
            $amount->price = $sum_price;
            $this->savedRecord->amount()->save($amount);        
        }

        // save movement id to allocation
        if ($movement) {
            $this->savedRecord->movement_id = $movement->id;
            $this->savedRecord->save();
        }
        
        return true;
    }


    private function generateMovement($data, $type, $concept, $allocation_type)
    {
        $id_allocation = $this->savedRecord->id;

        switch ($allocation_type) {
            case 'E' : 
                $txt = 'entrega';
                break;
            case 'D' : 
                $txt = 'devolución';
                break;
        }

        $mov = new Movement;
        $mov->mov_date     = $data['rec_date'];
        $mov->type         = $type;
        $mov->warehouse_id = $data['warehouse_id'];
        $mov->concept_id   = $concept->id;
        $mov->is_automatic = true;
        $mov->user_id      = session('userID');
        $mov->comments     = "Movimiento automático por proceso de $txt: #$id_allocation";
        $mov->save();

        foreach ($data['details'] as $key => $item) {
            $mv = new MovementBrand;
            $mv->brand_id = $item['brand']['id'];
            $mv->quantity = $item['quantity'];

            $mov->details()->save($mv);

            // updates stock
            if ($type == 'E') {
                Stock::addStock($mv->brand->id, $data['warehouse_id'], $item['quantity']);
            } elseif ($type == 'S') {
                Stock::substractStock($mv->brand->id, $data['warehouse_id'], $item['quantity']);
            }
        }

        return $mov;
    }

    private function generateCancellation($comments)
    {
        return AllocationCancellation::create([
            'cancel_date' => date('Y-m-d H:i:s'),
            'user_id' => session('userID'),
            'comments' => $comments
        ]);
    }

    private function cancelMovement($movement_id, $type)
    {
        $mov = Movement::find($movement_id);

        $cancellation = $this->generateMovementCancellation($type);

        $mov->cancellation_id = $cancellation->id;
        $mov->active = false;
        
        if ($mov->save()) {
            foreach ($mov->details as $item) {
                if ($mov->type == 'E') {
                    Stock::substractStock($item->brand_id, $mov->warehouse_id, $item['quantity']);
                } else {
                    Stock::addStock($item->brand_id, $mov->warehouse_id, $item['quantity']);
                }
            }
        }
    }

    private function generateMovementCancellation($type)
    {
        switch ($type) {
            case 'E':
                $txt = "entrega";
                break;
            case 'D':
                $txt = "devolución";
                break;
            case 'L':
                $txt = "liquidación";
                break;
        }

        return MovementCancellation::create([
            'cancel_date' => date('Y-m-d H:i:s'),
            'user_id' => session('userID'),
            'comments' => "Cancelación automática generada por cancelación de $txt"
        ]);
    }
}
