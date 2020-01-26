<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MovementConceptsController extends BaseController
{
    protected $mainModel = 'App\MovementConcept';

    // params needen for index
    protected $searchFields = ['name'];
    protected $indexPaginate = 10;
    protected $indexJoins = [];

    // params needer for show
    protected $showJoins = [];

    // params needed for store/update
    // protected $saveFields = [];
    // protected $storeFields = [];
    // protected $updateFields = [];
    protected $defaultNulls = [];
    protected $formRules = [
        'name' => 'required|min:5'
    ];

    protected $allowDelete = false;
    protected $allowUpdate = false;
    protected $allowStore  = false;
    protected $except = [];

    protected $useTransactions = false;
}
