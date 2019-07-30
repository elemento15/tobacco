<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BrandsController extends BaseController
{
    protected $mainModel = 'App\Brand';

    // params needen for index
    protected $searchFields = ['name'];
    protected $indexPaginate = 10;
    protected $indexJoins = [];

    // params needer for show
    protected $showJoins = [];

    // params needed for store/update
    protected $saveFields = ['name','packs_per_box','cost'];
    // protected $storeFields = [];
    // protected $updateFields = [];
    protected $defaultNulls = [];
    protected $formRules = [
        'name' => 'required|min:5',
        'packs_per_box' => 'required|min:1',
        'cost' => 'required|min:1',
    ];

    protected $allowDelete = true;
    protected $allowUpdate = true;
    protected $allowStore  = true;
    protected $except = [];

    protected $useTransactions = false;
}
