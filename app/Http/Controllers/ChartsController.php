<?php

namespace App\Http\Controllers;

use App\Allocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

class ChartsController extends Controller
{
    public function sales()
    {
        $records = [];
        $date = Carbon::now()->startOfMonth()->addMonth(-11);

        $data = DB::table('allocations AS a')
                  ->join('allocation_amounts AS aa', 'aa.allocation_id', '=', 'a.id')
                  ->select(DB::raw('DATE_FORMAT(a.rec_date, "%Y-%m") AS period, SUM(aa.price) AS amount'))
                  ->where('a.rec_date', '>', $date->format('Y-m-d'))
                  ->where('a.type', 'L')
                  ->where('a.active', true)
                  ->groupBy('period')
                  ->orderBy('period')
                  ->get();

        for ($i=0; $i < 12; $i++) {
            if ($data[$i] ?? false) {
                $records[] = [
                    'period' => $date->format('Y-m'),
                    'amount' => $data[$i]->amount
                ];
            } else {
                $records[] = [
                    'period' => $date->format('Y-m'),
                    'amount' => 0
                ];
            }

            $date->addMonth();
        }

        foreach ($records as $key => $item) {
            $dt = Carbon::parse($item['period']);
            $records[$key]['period'] = $dt->year .'-'. ucfirst($dt->locale('es')->shortMonthName);
        }

        return Response::json($records);
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

    public function weekly()
    {
        $records = collect();
        $date = Carbon::now()->addYear(-1);

        $data = DB::table('allocations AS a')
                  ->join('allocation_amounts AS aa', 'aa.allocation_id', '=', 'a.id')
                  ->select(DB::raw('YEAR(a.rec_date) AS year_num, WEEK(a.rec_date, 1) AS week_num, SUM(aa.price) AS amount'))
                  ->where('a.rec_date', '>=', $date->format('Y-m-d'))
                  ->where('a.type', 'L')
                  ->where('a.active', true)
                  ->groupBy('year_num')
                  ->groupBy('week_num')
                  ->orderBy('year_num')
                  ->orderBy('week_num')
                  ->get();
        
        foreach ($data as $key => $item) {
            if ($key == 0) continue; // omit first week

            $records->push([
                'year_num' => $item->year_num,
                'week_num' => $item->week_num,
                'label'    => $this->getWeekLabel($item->year_num, $item->week_num),
                'amount'   => $item->amount,
            ]);
        }

        return Response::json($records);
    }


    private function getWeekLabel($year, $week)
    {
        $dt = new DateTime();
        $date = $dt->setISODate($year, $week, "1")->format('Y-m-d');
        
        $dt = Carbon::create($date);
        $day  = $dt->format('d');
        $year = $dt->format('Y');
        return $day .'/'. ucfirst($dt->locale('es')->shortMonthName) .'/'. $year;
    }
}
