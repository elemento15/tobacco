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
    //protected $saveFields = ['name','packs_per_box','cost'];
    protected $storeFields = ['name','packs_per_box','cost','price'];
    protected $updateFields = ['name','cost','price'];
    protected $defaultNulls = [];
    protected $formRules = [
        'packs_per_box' => 'required|numeric|min:1|max:9999',
        'cost'  => 'required|numeric|min:1|max:9999',
        'price' => 'required|numeric|min:1|max:9999',
        'name' => 'unique:brands,name,{{id}}|required|min:3',
    ];

    protected $allowDelete = true;
    protected $allowUpdate = true;
    protected $allowStore  = true;
    protected $except = [];

    protected $useTransactions = false;
}
