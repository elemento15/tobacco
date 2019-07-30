<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends BaseController
{
    protected $mainModel = 'App\User';

    // params needen for index
    protected $searchFields = ['name','email'];
    protected $indexPaginate = 10;
    protected $indexJoins = ['role'];

    // params needer for show
    protected $showJoins = ['role'];

    // params needed for store/update
    // protected $saveFields = [];
    protected $storeFields = [];
    protected $updateFields = ['name'];
    protected $defaultNulls = [];
    protected $formRules = [
        'name' => 'required|min:8'
    ];

    protected $allowDelete = false;
    protected $allowUpdate = true;
    protected $allowStore  = false;
    protected $except = [];

    protected $useTransactions = false;


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $filters = $request->filters;
        $order = $request->order;
        $model = new $this->mainModel;

        // set relationships
        if (isset($this->indexJoins) && count($this->indexJoins)) {
            $model = $model->with($this->indexJoins);
        }

        if ($filters) {
            $model = $model->where(function ($query) use ($filters) {
                foreach ($filters as $item) {
                    if (isset($item['isNull'])) {
                        $query = $query->where($item['field'], NULL);
                    } else {
                        $query = $query->where($item['field'], $item['value']);
                    }
                }
            });
        }

        // exclude users with SYS role
        $model = $model->whereHas('role', function ($query) {
            $query->where('code', '!=', 'SYS');
        });
        
        if ($search) {
            $model = $model->where(function ($query) use ($search) {
                foreach ($this->searchFields as $key => $field) {
                    if ($key == 0) {
                        $query = $query->where($field, 'like', '%'.$search.'%');
                    } else {
                        $query = $query->orWhere($field, 'like', '%'.$search.'%');
                    }
                }
            });
        }

        if ($order) {
            $model = $model->orderBy($order['field'], $order['type']);
        }

        if ($request->page) {
            $model = $model->paginate($this->indexPaginate);
        } else {
            $model = $model->get();
        }

        return $model;
    }
}
