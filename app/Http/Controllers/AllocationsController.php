<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuration;

class AllocationsController extends BaseController
{
    protected $mainModel = 'App\Allocation';

    // params needen for index
    protected $searchFields = ['rec_date'];
    protected $indexPaginate = 10;
    protected $indexJoins = [];

    // params needer for show
    protected $showJoins = [];

    // params needed for store/update
    protected $saveFields = ['rec_date','salesperson_id','warehouse_id','type','user_id','comments'];
    //protected $storeFields = [];
    //protected $updateFields = [];
    protected $defaultNulls = ['warehouse_id','user_id','cancel_user_id','cancel_date','comments'];
    protected $formRules = [
        'salesperson_id' => 'required',
        'type' => 'required',
    ];

    protected $allowDelete = false;
    protected $allowUpdate = false;
    protected $allowStore  = true;
    protected $except = [];

    protected $useTransactions = true;


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

        // get allocation warehouse default
        $warehouse = Configuration::getAllocationWarehouse();

        $this->request['rec_date'] = date('Y-m-d H:i:s');
        $this->request['salesperson_id'] = $req->salesperson_id;
        $this->request['warehouse_id'] = $warehouse->id;
        $this->request['type'] = $req->type;
        $this->request['user_id'] = session('userID');
        $this->request['comments'] = $req->comments;
        $this->request['details'] = $req->details;

        return true;
    }
}
