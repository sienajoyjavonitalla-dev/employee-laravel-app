<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobs;
use App\Models\Appointment;
use DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\User;
use App\Models\JobAssignee;
use Carbon\Carbon;

class JobsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {

            if ($request->name == 'list') {
                $data = DB::table('jobs as j')
                    ->leftJoin('invoices as i', 'i.job_id', '=', 'j.id')
                    ->leftJoin('clients as c', 'c.id', '=', 'j.client_id')
                    ->selectRaw('j.id, j.client_id, j.status, j.address, j.start_date_time, j.end_date_time, j.no_of_persons, i.invoice_id, i.invoice_url, c.company_name')
                    ->get();
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('invoice', function($row){
                        if($row->invoice_id) {
                            $inv = "<a href='".$row->invoice_url."'> ".$row->invoice_id."</a>";
                        } else {
                            $inv = '<a href="javascript:void(0)" data-compid="'.$row->client_id.'"  data-company="'.$row->company_name.'"  data-id="'.$row->id.'" data-toggle="tooltip" class="btn btn-secondary btn-xs generateBtn"><i class="fa-solid fa-gear"></i>Generate</a>';

                        }
                        return $inv;
                    })
                    ->addColumn('client', function($row){
                        $client = Client::where('id', $row->client_id)->first();
                        return $client->company_name;
                    })
                    ->addColumn('assigned', function($row){
                        $assigned = DB::table('job_assignee as ja')
                            ->leftJoin('users as u', 'ja.assigned_id', '=', 'u.id')
                            ->where('ja.job_id', $row->id)
                            ->select('u.name')
                            ->get();
                        $display = "";

                        if($assigned->count() > 0) {
                            $count=0;
                            foreach($assigned as $a) {
                                if($count > 0)
                                    $display .=', ';
    
                                $display .=$a->name;
                                $count++;
                            }

                        } 
                            $display .= '<a href="javascript:void(0)"  data-id="'.$row->id.'" data-toggle="tooltip" class="btn-xs assignBtn"><i class="fas fa-pen"></i></a>';


                        
                        return $display;
                    })
                    ->addColumn('action', function($row){
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editJob">Edit</a>';
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteJob">Delete</a>';

                        return $btn;
                    })
                    ->rawColumns(['action', 'assigned', 'invoice'])
                    ->make(true);
            } else if ($request->name == 'generate') {
                $data = DB::table('time_logs as tl')
                    ->leftJoin('jobs as jobs', 'job_id', '=', 'jobs.id')
                    ->leftJoin('clients as clients', 'jobs.client_id', '=', 'clients.id');

                
                if ($request->filled('company_id')) {
                    $data = $data->where('client_id', $request->company_id);
                }

                if ($request->filled('job_id')) {
                    $data = $data->where('tl.job_id', $request->job_id);
                }

                $data = $data->selectRaw('jobs.id, title, po_number, assigned_id, job_id, tl.start_time, tl.end_time, date, client_id, company_name, rate_per_hour, ot_rate_per_hour, lunch_break');
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
                        }

                        
                        return $pay;
                    })
                    ->addColumn('ot_pay', function($row){
                        $total_hr = 0;
                        $start_time = new Carbon($row->start_time);
                        $end_time =new Carbon($row->end_time);
                        $total_hr = $start_time->diffInHours($end_time);
                        $ot_pay = 0;
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
                            }
                            $total_amount = $ot_pay + $pay;
                        } else if($total_hr > 0 && $total_hr <= 4) {
                            $pay = 4 * $row->rate_per_hour;
                            $total_amount = $pay;
                        }
                        
                        return $total_amount;
                    })
                    ->make(true);
            } 
        }

        $clients = Client::pluck('company_name', 'id');
        $assigned = User::whereIn('roles', ['subcontractor', 'full-timer'])->pluck('name', 'id');

        return view('jobs.index', compact('clients', 'assigned'));

    }
    public function assigned_index(Request $request, Jobs $job)
    {
        if ($request->ajax()) {

            $data = JobAssignee::leftJoin('users as u', 'job_assignee.assigned_id', '=', 'u.id')
                ->where('job_id', $job->id)
                ->selectRaw('job_assignee.id, name, job_title')
                ->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteJobAssign">Delete</a>';

                        return $btn;
                    })
                    ->rawColumns(['action', 'assigned'])
                    ->make(true);
        }

        $clients = Client::pluck('company_name', 'id');
        $assigned = User::whereIn('roles', ['subcontractor', 'full-timer'])->pluck('name', 'id');

        return view('jobs.index', compact('clients', 'assigned'));

    }

    public function assigned_delete(Request $request, JobAssignee $assign)
    {
        JobAssignee::find($assign->id)->delete();

        return response()->json(['success'=>'Deleted successfully.']);

    }

    public function show(Jobs $id)
    {
        return view('jobs.index', ['Jobs' => $id]);
    }

    public function create()
    {
        return view('jobs.create', ['jobs' => new Jobs()]);
    }

    public function store(Request $request)
    {
        $status = "open";

        if($request->employee_id) 
            $status = "assigned";
        DB::transaction(function() use ($request, $status) {

            $job = Jobs::updateOrCreate(
                [
                    'id' => $request->job_id
                ],
                [
                    'client_id' => $request->client_id,
                    'employee_id' => $request->employee_id,
                    'status' => $status,
                    'po_number' => $request->po_number,
                    'description' => $request->description,
                    'address' => $request->address,
                    'start_date_time' => $request->start_date_time,
                    'end_date_time' => $request->end_date_time,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'no_of_persons' => $request->no_of_persons,
                ]
            );

            Appointment::updateOrCreate(
                [
                    'job_id' => $job->id
                ],
                [
                    'start_time' => $request->start_date_time,
                    'finish_time' => $request->start_date_time,
                    'title' => $request->title,
                    'job_id' => $job->id,
                    'client_id' => $request->client_id,
                    'user_id' => $request->employee_id
                ]
            );

        },2);

        return response()->json(['success'=>'Job saved successfully.']);
    }

    public function store_assigned(Request $request)
    {
        $job = Jobs::where('id', $request->job_assignee_id)->first();
        $assigned = JobAssignee::where('job_id', $request->job_assignee_id)->get()->count();

        if( $assigned < $job->no_of_persons ) {
            JobAssignee::updateOrCreate(
                ['job_id' => $request->job_assignee_id,
                'assigned_id' => (int)$request->assigned_id],
                [
                    'job_id' => $request->job_assignee_id,
                    'assigned_id' => (int)$request->assigned_id,
                    'job_title' => $request->job_title
                ]
            );

            return response()->json(['success'=>'Job Assigned successfully.']);
        } else {
            return response()->json(['error'=>'Job Assigned is full already']);

        }
    }

    public function edit($id)
    {
        $jobs = Jobs::find($id);
        return response()->json($jobs);
    }

    public function destroy($id)
    {

        DB::transaction(function() use($id) {

            Jobs::find($id)->delete();
            Appointment::where('job_id', $id)->delete();

        },2);        

        return response()->json(['success'=>'Job deleted successfully.']);
    }
}
