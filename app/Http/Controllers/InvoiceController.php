<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobs;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use App\Models\TimeLog;
use DataTables;
use Carbon\Carbon;
use PDF;
use DB;
class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $data = DB::table('time_logs as tl')
            ->leftJoin('jobs as j', 'tl.job_id', '=', 'j.id')
            ->leftJoin('clients as c', 'j.client_id', '=', 'c.id')
            ->leftJoin('job_assignee as ja', 'j.id', '=', 'ja.job_id')
            ->leftJoin('users as u', 'u.id', '=', 'ja.assigned_id')
            ->whereNotNull('tl.end_time')
            ->whereNotNull('ja.assigned_id')
            ->whereNotNull('j.id')
            ->where('u.roles', 'subcontractor');

        $filter = $data;

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $data = $data->whereBetween('date', [(string)$request->from_date, (string)$request->to_date]);
        }

        if ($request->filled('client')) {
            $data = $data->where('j.client_id', $request->client);
        }

        if ($request->filled('assigned')) {
            $data = $data->where('ja.assigned_id', $request->assigned);
        }

        if ($request->filled('job')) {
            $data = $data->where('j.id', $request->job);
        }

        
        
        if ($request->ajax()) {
            if ($request->name == 'generate') {
                $data = $data->selectRaw('j.id, j.address, ja.job_title, ja.assigned_id, u.name, tl.job_id, tl.start_time, tl.end_time, date, 
                    client_id, company_name, u.rate_per_hour, u.ot_rate_per_hour, lunch_break')->orderBy('date')->get();
                return Datatables::of($data)
                    ->addIndexColumn()
                    // ->addColumn('employee', function($row){
                    //     $qry = User::where('id', $row->assigned_id)->first();
                    //     $user = $qry->name;
                    //     return $user;
                    // })
                    ->addColumn('hrs_worked', function($row){
                        $total_hr = 0;
                        $start_time = new Carbon($row->start_time);
                        $end_time =new Carbon($row->end_time);
                        $total_hr = $start_time->diffInHours($end_time);
                        if($row->lunch_break) {
                            $total_hr = $total_hr - .5;
                        }
                        return $total_hr;
                    })
                    ->addColumn('with_lunch', function($row){
                        return $row->lunch_break ? 'Yes':'No';
                    })
                    ->addColumn('pay', function($row){
                        $total_hr = 0;
                        $start_time = new Carbon($row->start_time);
                        $end_time =new Carbon($row->end_time);
                        $total_hr = $start_time->diffInHours($end_time);
                        $pay = 0;
                        
                        if($row->lunch_break) {
                            $total_hr = $total_hr - .5;
                        }
                        
                        if($total_hr > 4 && $total_hr <= 8 ) {
                            $ot_pay=0;
                            $pay = $total_hr * $row->rate_per_hour;
                        } else if($total_hr > 0 && $total_hr <= 4) {
                            $pay = 4 * $row->rate_per_hour;
                        } else if($total_hr > 8) {
                            $total_hr = 8;
                            $pay = 8 * $row->rate_per_hour;
                        }
                        
                        return $pay;
                    })
                    ->addColumn('ot_pay', function($row){
                        $total_hr = 0;
                        $start_time = new Carbon($row->start_time);
                        $end_time =new Carbon($row->end_time);
                        $total_hr = $start_time->diffInHours($end_time);
                        $ot_pay = 0;

                        if($row->lunch_break) {
                            $total_hr = $total_hr - .5;
                        }
                        if($total_hr > 8) {
                            $ot_hours= $total_hr - 8;
                            $ot_pay = $ot_hours * $row->ot_rate_per_hour;
                        }
                        return $ot_pay;
                    })
                    ->addColumn('total', function($row){
                        $total_hr = 0;
                        $start_time = new Carbon($row->start_time);
                        $end_time =new Carbon($row->end_time);
                        $total_hr = $start_time->diffInHours($end_time);
                        $total_amount=0;
                        if($row->lunch_break) {
                            $total_hr = $total_hr - .5;
                        }    
                        if($total_hr > 4) {
                            $ot_pay=0;
                            $pay = $total_hr * $row->rate_per_hour;
                            if($total_hr > 8) {
                                $ot_hours= $total_hr - 8;
                                $ot_pay = $ot_hours * $row->ot_rate_per_hour;
                                $total_hr = 8;
                                $pay = $total_hr * $row->rate_per_hour;

                            }
                            $total_amount = $ot_pay + $pay;
                        } else if($total_hr > 0 && $total_hr <= 4) {
                            $pay = 4 * $row->rate_per_hour;
                            $total_amount = $pay;
                        }
                        
                        return $total_amount;
                    })
                    ->make(true);
            } else {
                $data = Invoice::where('job_id', '>', 0)->get();
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('online_invoice_url', function($row){

                        if($row->invoice_url) 
                            $url=$row->invoice_url;
                        else {
                            $url = '<a href="" data-toggle="tooltip" class="btn btn-secondary btn-xs">Generate</a>';
                        }
                        return $url;
                    })
                    ->addColumn('emailed', function($row){

                        if($row->is_emailed) 
                            $url="Yes";
                        else {
                            $url = '<a href="" data-toggle="tooltip"><i class="fas fa-envelope"></i></a> No ';
                        }
                        return $url;
                    })
                    ->addColumn('action', function($row){
                        $btn = '<a href="" data-toggle="tooltip" class="mr-1 btn btn-warning btn-sm">Authorize</a>';
                        $btn .= '<a href="" data-toggle="tooltip" class="mr-1 btn btn-primary btn-sm">Update</a>';
                        $btn .= '<a href="" data-toggle="tooltip" class="btn btn-danger btn-sm">Void</a>';

                        return $btn;
                    })
                    ->rawColumns(['action', 'online_invoice_url','emailed'])
                    ->make(true);
            } 
        }
        $clients = Client::pluck('company_name', 'id');
        $assigned = User::whereIn('roles', ['subcontractor', 'full-timer'])->pluck('name', 'id');

        $jobs = Jobs::pluck('address', 'id');
       
        $filter_assigned = $filter->where('u.roles', 'subcontractor')->selectRaw('DISTINCT u.name, ja.assigned_id')->get();
        return view('invoices.index', compact('clients', 'jobs', 'filter_assigned'));

    }
    public function generatePDF(Request $request)
    {
        $data = DB::table('time_logs as tl')
            ->leftJoin('jobs as j', 'tl.job_id', '=', 'j.id')
            ->leftJoin('clients as c', 'j.client_id', '=', 'c.id')
            ->leftJoin('job_assignee as ja', 'j.id', '=', 'ja.job_id')
            ->leftJoin('users as u', 'u.id', '=', 'ja.assigned_id')
            ->whereNotNull('tl.end_time')
            ->whereNotNull('ja.assigned_id')
            ->whereNotNull('j.id')
            ->where('u.roles', 'subcontractor');

        if ($request->from_date && $request->to_date) {
            $data = $data->whereBetween('date', [(string)$request->from_date, (string)$request->to_date]);
        }

        if ($request->client) {
            $data = $data->where('j.client_id', $request->client);
        }

        if ($request->assigned) {
            $data = $data->where('ja.assigned_id', $request->assigned);
        }

        if ($request->job) {
            $data = $data->where('j.id', $request->job);
        }
        
        $data = $data->selectRaw('j.id, j.address, j.po_number, ja.job_title, ja.assigned_id, u.name, tl.job_id, tl.start_time, tl.end_time, date, 
                    client_id, company_name, u.rate_per_hour, u.ot_rate_per_hour, lunch_break')->orderBy('date');// $this->convert_customer_data_to_html($data);
        // dd($data->get());
        $data = $data->get();
        $first = $data->first();
        $po_number = $first->po_number;
        $dataArr = array(
            'first' => $first,
            'data' => $data,
        );
        // dd($dataArr);
        //uncomment later
        // view()->share('dataArr',$dataArr);       
        // $pdf = PDF::loadView('invoices.pdf_view');
        // $pdf->download('pdf_view.pdf');

        $pdf = \App::make('dompdf.wrapper');
        $pdf =PDF::loadView('invoices.pdf_view',compact('dataArr'));

        return $pdf->stream('pdf_view.pdf');
        // return view('invoices.pdf_view', compact('dataArr'));

    }

    public function pdf()
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convert_customer_data_to_html($req));
        return $pdf->stream();
    }

    public function convert_customer_data_to_html($data)
    {
    //  $customer_data = $this->get_customer_data();

    $output = '
        <h3 align="center">Customer Data</h3>
        <table width="100%" style="border-collapse: collapse; border: 0px;">
        <tr>
            <th style="border: 1px solid; padding:12px;" width="20%">Name</th>
            <th style="border: 1px solid; padding:12px;" width="30%">Address</th>
            <th style="border: 1px solid; padding:12px;" width="15%">City</th>
            <th style="border: 1px solid; padding:12px;" width="15%">Postal Code</th>
            <th style="border: 1px solid; padding:12px;" width="20%">Country</th>
        </tr>
     ';  
     foreach($data as $d)
     {
      $output .= '
      <tr>
       <td style="border: 1px solid; padding:12px;">'.$customer->CustomerName.'</td>
       <td style="border: 1px solid; padding:12px;">'.$customer->Address.'</td>
       <td style="border: 1px solid; padding:12px;">'.$customer->City.'</td>
       <td style="border: 1px solid; padding:12px;">'.$customer->PostalCode.'</td>
       <td style="border: 1px solid; padding:12px;">'.$customer->Country.'</td>
      </tr>
      ';
     }
     $output .= '</table>';
     return $output;
    }
}
