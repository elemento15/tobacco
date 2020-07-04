<?php

namespace App\Http\Controllers;

use App\Allocation;
use Illuminate\Http\Request;
use Response;
use DB;
use Carbon\Carbon;

class ChartsController extends Controller
{
    public function sales()
    {
        $data = DB::table('allocations AS a')
                  ->join('allocation_amounts AS aa', 'aa.allocation_id', '=', 'a.id')
                  ->select(DB::raw('DATE_FORMAT(a.rec_date, "%Y-%m") AS period, SUM(aa.price) AS amount'))
                  ->where('a.type', 'L')
                  ->where('a.active', true)
                  ->groupBy('period')
                  ->orderBy('period', 'desc')
                  ->limit(12)
                  ->get();

        foreach ($data as $key => $item) {
            $dt = Carbon::parse($item->period);
            $data[$key]->period = $dt->year .'-'. ucfirst($dt->locale('es')->shortMonthName);
        }

        return Response::json(array_reverse($data->toArray()));
    }

    public function salesperson()
    {
        $dt = Carbon::now();

        $data = DB::table('allocations AS a')
                  ->join('allocation_amounts AS aa', 'aa.allocation_id', '=', 'a.id')
                  ->join('salespeople AS s', 's.id', '=', 'a.salesperson_id')
                  ->select(DB::raw('s.name, SUM(aa.price) AS amount'))
                  ->where('a.rec_date', '>=', $dt->subDays(30)->format('Y-m-d'))
                  ->where('a.type', 'L')
                  ->where('a.active', true)
                  ->groupBy('s.id')
                  ->orderBy('amount', 'desc')
                  ->get();

        return Response::json($data);
    }
}
