<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BrandTypesController extends BaseController
{
    protected $mainModel = 'App\BrandType';

    // params needen for index
    protected $searchFields = ['name','code'];
    protected $indexPaginate = 10;
    protected $indexJoins = [];

    // params needer for show
    protected $showJoins = [];

    // params needed for store/update
    protected $saveFields = ['name','code','warehouse_id'];
    //protected $storeFields = ['name','code'];
    //protected $updateFields = ['name','code'];
    protected $defaultNulls = [];
    protected $formRules = [
        'name'  => 'required|min:5|max:15',
        'code' => 'unique:brand_types,code,{{id}}|required|min:5|max:15'
    ];

    protected $allowDelete = false;
    protected $allowUpdate = false;
    protected $allowStore  = false;
    protected $except = [];

    protected $useTransactions = true;
}
