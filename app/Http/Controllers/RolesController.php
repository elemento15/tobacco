<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RolesController extends BaseController
{
    protected $mainModel = 'App\Role';

    // params needen for index
    protected $searchFields = ['name','code'];
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
        'name' => 'required|min:5',
        'code' => 'required|min:3',
    ];

    protected $allowDelete = false;
    protected $allowUpdate = false;
    protected $allowStore  = false;
    protected $except = [];

    protected $useTransactions = false;
}
