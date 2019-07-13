<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        'name' => 'required|min:8',
    ];

    protected $allowDelete = true;
    protected $allowUpdate = true;
    protected $allowStore  = true;
    protected $except = [];
}
