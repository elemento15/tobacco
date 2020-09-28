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
                    'ini_date'  => $req->ini_date . ' 00:00:00', 
                    'end_date'  => $req->end_date . ' 23:59:59',
                    'type'      => $req->brand_type_id ?? false,
                    'omit_zero' => intval($req->omit_zero)
                ];
                $data = $rpt->getDateRangeSales($params);
                break;

            case 'SalesPersonSummary':
                $params = [
                    'type'      => $req->brand_type_id ?? false,
                    'omit_zero' => intval($req->omit_zero)
                ];
                $data = $rpt->getSalesPersonSummary($params);
                break;

            case 'Cancellations':
                $params = [
                    'ini_date' => $req->ini_date . ' 00:00:00',
                    'doc_type' => $req->doc_type
                ];
                $data = $rpt->getCancellations($params);
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
                    'end_date' => $req->end_date . ' 23:59:59',
                    'type'      => $req->brand_type_id ?? false,
                    'omit_zero' => intval($req->omit_zero)
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

            case 'SalesPersonSummary':
                $params = [
                    'type'      => $req->brand_type_id ?? false,
                    'omit_zero' => intval($req->omit_zero)
                ];
                $data = [
                    'data' => $rpt->getSalesPersonSummary($params),
                    'sum_packs' => 0,
                    'sum_boxes' => 0,
                    'sum_amount' => 0,
                ];
                $view = 'reports/salesperson_summary';
                break;
        }

        $pdf = PDF::loadView($view, $data);
        return $pdf->stream('rpt_download.pdf', ['Attachment' => false]);
    }
}
