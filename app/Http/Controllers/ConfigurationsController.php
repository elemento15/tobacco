<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Configuration;

class ConfigurationsController extends Controller
{
    public function get()
    {
    	if (! $config = Configuration::first()) {
    		$config = [
    			'default_warehouse_id' => '',
    			'allocation_warehouse_id' => '',
                'role_code' => session('roleCode'),
    		];
    	}

    	return Response::json($config);
    }

    public function save(Request $request)
    {
    	if (! $config = Configuration::first()) {
    		$config = new Configuration;
    	}

		$config->default_warehouse_id = $request->default_warehouse_id;
		$config->allocation_warehouse_id = $request->allocation_warehouse_id;
		$config->save();

		return Response::json($config);
    }
}