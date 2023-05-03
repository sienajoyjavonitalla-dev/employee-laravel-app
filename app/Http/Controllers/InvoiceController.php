<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobs;
use App\Models\Client;
use App\Models\User;
use App\Models\TimeLog;
use DataTables;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            //testvariables
            $assigned=1;
            $job_id=3;
            // $data = TimeLog::where('assigned_id', $request->assigned)->where('job_id', $request->job_id);
            $data = TimeLog::leftJoin('jobs', 'job_id', '=', 'jobs.id')
                ->leftJoin('clients', 'jobs.client_id', '=', 'clients.id')
                ->where('assigned_id', $assigned)
                ->where('job_id', $job_id);

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

            $data = $data->selectRaw('jobs.id, jobs.title, jobs.po_number, assigned_id, job_id, start_time, end_time, date, client_id, client_name, rate_per_hour, ot_rate_per_hour');
            // if((string)$request->to_date == '2023-04-20')
            // dd($data->get());
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
}
