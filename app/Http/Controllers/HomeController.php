<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Reports;
use PDF;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home', ['role' => session('roleCode')]);
    }

    public function reports(Request $req)
    {
        $rpt = new Reports();
        $type = $req->type;
        $data = [];

        switch ($type) {
            case 'DateRangeSales':
                $params = [
                    'ini_date' => $req->ini_date . ' 00:00:00', 
                    'end_date' => $req->end_date . ' 23:59:59'
                ];
                $data = $rpt->getDateRangeSales($params);
                break;
        }

        return $data;
    }

    public function download(Request $req)
    {
        $rpt = new Reports();
        $type = $req->type;
        $data = [];

        switch ($type) {
            case 'DateRangeSales':
                $params = [
                    'ini_date' => $req->ini_date . ' 00:00:00', 
                    'end_date' => $req->end_date . ' 23:59:59'
                ];
                $data = [
                    'data' => $rpt->getDateRangeSales($params),
                    'ini_date'  => Carbon::parse($params['ini_date'])->format('d/m/Y'),
                    'end_date'  => Carbon::parse($params['end_date'])->format('d/m/Y'),
                    'sum_price' => 0,
                    'sum_cost'  => 0,
                    'sum_items' => 0,
                    'sum_packs' => 0,
                ];
                $view = 'reports/date_range_sales';
                break;
        }

        $pdf = PDF::loadView($view, $data);
        return $pdf->stream('rpt_download.pdf', ['Attachment' => false]);
    }
}
