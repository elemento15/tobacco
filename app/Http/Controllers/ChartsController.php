<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
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
    
        // list of months to show in the chart
        $dates = [];
        for ($i=0; $i < 12; $i++) {
            $dates[] = $date->format('Y-m');
            $date->addMonth();
        }
        
        // fill the records with the data, if no data for a month, set amount to 0
        foreach ($dates as $i => $period) {
            $record = ['period' => $period, 'amount' => 0];
            foreach ($data as $d) {
                if ($d->period == $period) {
                    $record['amount'] = $d->amount;
                    break;
                }
            }
            $records[] = $record;
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
                  ->where('a.rec_date', '>=', $date->format('Y-m-d 00:00:00'))
                  ->where('a.type', 'L')
                  ->where('a.active', true)
                  ->groupBy('year_num')
                  ->groupBy('week_num')
                  ->orderBy('year_num')
                  ->orderBy('week_num')
                  ->get();
        
        // get list of weeks to show in the chart, staring from the current week and going back 52 weeks
        $today = Carbon::now();
        $weeks = [];
        for ($i=0; $i < 52; $i++) {
            $weeks[] = [
                'year_num' => $today->year,
                'week_num' => $today->weekOfYear,
            ];
            $today->subWeek();
        }
        
        // fill the records with the data, if no data for a week, set amount to 0
        foreach ($weeks as $i => $week) {
            $record = [
                'year_num' => $week['year_num'],
                'week_num' => $week['week_num'],
                'amount' => 0,
                'label' => $this->getWeekLabel($week['year_num'], $week['week_num']),
            ];
            foreach ($data as $d) {
                if ($d->year_num == $week['year_num'] && $d->week_num == $week['week_num']) {
                    $record['amount'] = $d->amount;
                    break;
                }
            }
            $records->push($record);
        }

        // invert the records so that the oldest week is first and the most recent week is last
        $records = $records->reverse()->values();

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
