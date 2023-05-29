<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobs;
use App\Models\Client;
use App\Models\User;
use App\Models\TimeLog;
use DataTables;
use Carbon\Carbon;
use PDF;
use DB;
class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = DB::table('time_logs as tl')->leftJoin('jobs as jobs', 'job_id', '=', 'jobs.id')
                ->leftJoin('clients as clients', 'jobs.client_id', '=', 'clients.id');

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $data = $data->whereBetween('date', [(string)$request->from_date, (string)$request->to_date]);
                    
            }

            if ($request->filled('client')) {
                $data = $data->where('client_id', $request->client);
            }

            if ($request->filled('po_number')) {
                $data = $data->where('po_number', $request->po_number);
            }

            if ($request->filled('assigned')) {
                $data = $data->where('assigned_id', $request->assigned);
            }

            $data = $data->selectRaw('jobs.id, title, po_number, assigned_id, job_id, start_time, end_time, date, client_id, client_name, rate_per_hour, ot_rate_per_hour');
            // if((string)$request->to_date == '2023-04-20')
            // dd($data->toSql());
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('employee', function($row){
                        $qry = User::where('id', $row->assigned_id)->first();
                        $user = $qry->name;
                        return $user;
                    })
                    ->addColumn('hrs_worked', function($row){
                        $total_hr = 0;
                        $start_time = new Carbon($row->start_time);
                        $end_time =new Carbon($row->end_time);
                        $total_hr = $start_time->diffInHours($end_time);
                        return $total_hr;
                    })
                    ->addColumn('pay', function($row){
                        $total_hr = 0;
                        $start_time = new Carbon($row->start_time);
                        $end_time =new Carbon($row->end_time);
                        $total_hr = $start_time->diffInHours($end_time);
                        $pay = 0;
                        if($total_hr >= 8)
                            $pay = 8 * $row->rate_per_hour;
                        return $pay;
                    })
                    ->addColumn('ot_pay', function($row){
                        $total_hr = 0;
                        $start_time = new Carbon($row->start_time);
                        $end_time =new Carbon($row->end_time);
                        $total_hr = $start_time->diffInHours($end_time);
                        $pay = 0;
                        if($total_hr > 8) {
                            $ot_hours= $total_hr - 8;
                            $pay = $ot_hours * $row->ot_rate_per_hour;
                        }
                        return $pay;
                    })
                    ->addColumn('total', function($row){
                        $total_hr = 0;
                        $start_time = new Carbon($row->start_time);
                        $end_time =new Carbon($row->end_time);
                        $total_hr = $start_time->diffInHours($end_time);
                        $pay = 0;
                        $ot_pay = 0;

                        if($total_hr >= 8) {
                            $pay = 8 * $row->rate_per_hour;
                            $ot_hours= $total_hr - 8;
                            $ot_pay = $ot_hours * $row->ot_rate_per_hour;
                        }
                        return $pay + $ot_pay;
                    })
                    ->make(true);
        }
        $clients = Client::pluck('client_name', 'id');
        $assigned = User::whereIn('roles', ['subcontractor', 'full timer'])->pluck('name', 'id');

        return view('invoices.index', compact('clients', 'assigned'));

    }
    public function generatePDF(Request $request)
    {
        // if(!$request->client || !$request->po_number) {
        //     return response()->json(['error'=>'Enter Client or PO number .']);
        // }
        $data = DB::table('time_logs as tl')->leftJoin('jobs as jobs', 'job_id', '=', 'jobs.id')
                ->leftJoin('clients as clients', 'jobs.client_id', '=', 'clients.id')
                ->leftJoin('users as u', 'jobs.employee_id', '=', 'u.id');

        if ($request->from_date && $request->to_date) {
            $data = $data->whereBetween('date', [(string)$request->from_date, (string)$request->to_date]);
        }

        if ($request->client) {
            $data = $data->where('client_id', $request->client);
        }

        if ($request->po_number) {
            $data = $data->where('po_number', $request->po_number);
        }

        if ($request->assigned) {
            $data = $data->where('assigned_id', $request->assigned);
        }

        $data = $data->selectRaw('name, travel_allowance, date, jobs.id, title, po_number, clients.address, assigned_id, job_id, start_time, end_time, date, client_id, client_name, company_name, rate_per_hour, ot_rate_per_hour, TIMESTAMPDIFF(HOUR, start_time, end_time)');
        // $this->convert_customer_data_to_html($data);
        $data = $data->get();
        $first = $data->first();
        $po_number = $first->po_number;
        $dataArr = array(
            'first' => $first,
            'data' => $data,
        );
        // dd($data);
        //uncomment later
        view()->share('dataArr',$dataArr);       
        $pdf = PDF::loadView('invoices.pdf_view');
        return $pdf->download('pdf_view.pdf');


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
