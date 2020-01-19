<?php

namespace App\Http\Controllers;

use App\Movement;
use App\MovementBrand;
use App\Stock;
use App\Warehouse;
use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;

class MovementsController extends BaseController
{
    protected $mainModel = 'App\Movement';

    // params needen for index
    protected $searchFields = ['id'];
    protected $indexPaginate = 10;
    protected $indexJoins = ['warehouse'];

    // params needer for show
    protected $showJoins = ['warehouse', 'details.brand'];

    // params needed for store/update
    // protected $saveFields = [];
    protected $storeFields = ['mov_date','type','warehouse_id','transfer_to','user_id','comments'];
    // protected $updateFields = [];
    protected $defaultNulls = ['transfer_to', 'transfer_from','cancel_user_id','cancel_date','user_id','comments'];
    protected $formRules = [];

    protected $allowDelete = false;
    protected $allowUpdate = false;
    protected $allowStore  = true;
    protected $except = [];

    protected $useTransactions = true;


    public function cancel($id)
    {
        $mainModel = $this->mainModel;
        $record = $mainModel::find($id);

        if (! $record) {
            return Response::json(array('msg' => 'Registro no encontrado'), 500);
        }

        if (! $record->active) {
            return Response::json(array('msg' => 'No puede cancelar un movimiento cancelado'), 500);
        }

        if ($record->transfer_from) {
            return Response::json(array('msg' => 'No puede cancelar una entrada generada por traspaso'), 500);
        }

        $record->active = 0;
        $record->cancel_date = date('Y-m-d H:i:s');
        $record->cancel_user_id = 1; // @LMNT set current user
        
        DB::beginTransaction();

        if ($record->save()) {
            // update stock
            foreach ($record->details as $item) {
                if ($record->type == 'E') {
                    Stock::substractStock($item->brand_id, $record->warehouse_id, $item['quantity']);
                } else {
                    Stock::addStock($item->brand_id, $record->warehouse_id, $item['quantity']);
                }
            }

            // review if is for tranfer
            if ($record->type == 'T') {
                $this->cancelTargetMovement($record->transfer_to);
            }

            DB::commit();
            return Response::json($record);
        } else {
            DB::rollback();
            return Response::json(array('msg' => 'Error al cancelar'), 500);
        }
    }


    protected function beforeStore()
    {
        $req = $this->request;

        if (! in_array($req->type, ['E', 'S', 'T'])) {
            $this->msgError = 'Tipo de inválido';
            return false;
        }

        if ($req->type == 'T') {
            if (! $target = Warehouse::find($req->warehouse_target)) {
                $this->msgError = 'Especifique el almacén destino';
                return false;
            }

            if ($target->id == $req->warehouse_id) {
                $this->msgError = 'Almacén origen y destino deben ser diferentes';
                return false;
            }
        }

        if (gettype($req->details) != 'array' || count($req->details) == 0) {
            $this->msgError = 'Agregue los detalles del movimiento';
            return false;
        }

        $this->request['mov_date'] = date('Y-m-d H:i:s');
        $this->request['type'] = $req->type;
        $this->request['warehouse_id'] = $req->warehouse_id;
        $this->request['warehouse_target'] = ($req->type == 'T') ? $target->id : null;
        $this->request['user_id'] = 1; // @LMNT: set current user
        $this->request['comments'] = $req->comments;
        $this->request['details'] = $req->details;

        return true;
    }

    protected function afterStore()
    {
        $details = $this->request['details'];
        $warehouse_id = $this->request['warehouse_id'];

        foreach ($details as $key => $item) {
            $mv = new MovementBrand;
            $mv->brand_id = $item['brand']['id'];
            $mv->quantity = $item['quantity'];

            $this->savedRecord->details()->save($mv);

            // updates stock
            if ($this->request['type'] == 'E') {
               Stock::addStock($mv->brand->id, $warehouse_id, $item['quantity']);
            } else { // S or T
                Stock::substractStock($mv->brand->id, $warehouse_id, $item['quantity']);
            }
        }

        // is a Transfer
        if ($this->request['type'] == 'T') {
            $mov = $this->generateTransfer($this->savedRecord);
            
            $this->savedRecord->transfer_to = $mov->id;
            $this->savedRecord->save();
        }
        
        return true;
    }


    private function generateTransfer($transfer)
    {
        $data = $this->request;
        $details = $this->request['details'];

        $mov = new Movement;
        $mov->mov_date = date('Y-m-d H:i:s');
        $mov->type = 'E';
        $mov->warehouse_id = $data['warehouse_target'];
        $mov->transfer_from = $transfer->id;
        $mov->user_id = 1; // @LMNT: set current user
        $mov->comments = '';
        $mov->save();

        foreach ($details as $key => $item) {
            $mv = new MovementBrand;
            $mv->brand_id = $item['brand']['id'];
            $mv->quantity = $item['quantity'];

            $mov->details()->save($mv);

            // updates stock
            Stock::addStock($mv->brand->id, $data['warehouse_target'], $item['quantity']);
        }

        return $mov;
    }

    private function cancelTargetMovement($id)
    {
        $mov = Movement::findOrFail($id);
        $mov->active = 0;
        $mov->cancel_user_id = 1; // @LMNT use current user
        $mov->cancel_date = date('Y-m-d H:i:s');
        $mov->comments = $mov->comments.' - Cancelación automática por ser entrada de un traspaso';
        $mov->save();

        foreach ($mov->details as $item) {
            Stock::substractStock($item->brand_id, $mov->warehouse_id, $item['quantity']);
        }
    }
}