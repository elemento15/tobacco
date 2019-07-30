<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WarehousesController extends BaseController
{
    protected $mainModel = 'App\Warehouse';

    // params needen for index
    protected $searchFields = ['name'];
    protected $indexPaginate = 10;
    protected $indexJoins = [];

    // params needer for show
    protected $showJoins = [];

    // params needed for store/update
    protected $saveFields = ['name'];
    // protected $storeFields = [];
    // protected $updateFields = [];
    protected $defaultNulls = [];
    protected $formRules = [
        'name' => 'required|min:5'
    ];

    protected $allowDelete = false;
    protected $allowUpdate = true;
    protected $allowStore  = true;
    protected $except = [];

    protected $useTransactions = false;
}
