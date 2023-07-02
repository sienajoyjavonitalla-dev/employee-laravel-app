<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Models\TimeLog;
use App\Models\Jobs;
use App\Models\JobAssignee;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use DB;
use App\Models\Client;

class LogTimeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if(Auth::user()->roles == 'admin') {
                $data = TimeLog::latest()->get();
            } else {
                $data = TimeLog::where('assigned_id', Auth::user()->id)->latest()->get();
            }

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('timesheet_url', function($row){
                        return "<a href=".url('/img/'.$row->timesheet)." target='_blank'>".$row->timesheet."</a>";
                    })

                    ->addColumn('assigned_to', function($row){
                        $qry = User::where('id', $row->assigned_id)->first();
                        $user = $qry->name;
                        return $user;
                    })
                    ->addColumn('with_lunch', function($row){
                        return $row->lunch_break ? 'Yes':'No';
                    })
                    ->addColumn('action', function($row){
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editTimeLog">Edit</a>';

                        if(Auth::user()->roles == 'admin') {
                            $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteTimeLog">Delete</a>';

                        }
                        
                        return $btn;
                    })
                    ->rawColumns(['action', 'timesheet_url'])
                    ->make(true);
        }
        $users = User::whereIn('roles', ['subcontractor', 'full-timer'])->pluck('name', 'id');
        $jobs = DB::table('jobs as j')
            ->leftJoin('clients as c', 'c.id', '=', 'j.client_id')
            ->where('j.status', '!=', 'complete')
            ->selectRaw('j.id, c.company_name, j.start_date_time, j.end_date_time')
            ->get();
        return view('timelogs.index',compact('users', 'jobs'));

    }
    public function signature()
    {
        return view('timelogs.signature-pad');
    }
    public function show(Jobs $id)
    {

        return view('timelogs.index', ['TimeLogs' => $id]);
    }

    public function timeclock()
    {
        $clock_in = TimeLog::where('assigned_id', Auth::user()->id)->whereNull('end_time')->orderBy('created_at', 'desc')->first();
       
        $job = Jobs::leftJoin('clients as clients', 'jobs.client_id', '=', 'clients.id')
            ->leftJoin('job_assignee as ja', 'ja.job_id', '=', 'jobs.id')
            ->where('ja.assigned_id', Auth::user()->id)
            ->whereDate('start_date_time', '<=', Carbon::now()->toDateString())
            // ->whereDate('end_date_time', '>=', Carbon::now()->toDateString())
            ->selectRaw('jobs.address, clients.company_name, jobs.id, ja.job_title as title, jobs.start_date_time, jobs.end_date_time, jobs.start_time, jobs.end_time')
            ->first();
        return view('timelogs.timeclock', compact('clock_in', 'job'));
    }

    public function store(Request $request)
    {
        $filename = '';

        if($request->file('timesheet')){
            $file= $request->file('timesheet');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('img'), $filename);
            // $data['image']= $filename;
        } else {
            if($request->id) {
                $tl=TimeLog::where('id', $request->id)->first();
                $filename = $tl->timesheet;
            }
        }
        
        TimeLog::updateOrCreate([
            'id' => $request->id
        ],
        [
            'job_id' => $request->job_id,
            'assigned_id' => $request->assigned_id,
            'lunch_break' => $request->has('lunch_break'),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'date' => $request->date,
            'timesheet' => $filename

        ]);

        return response()->json(['success'=>'Log saved successfully.']);
    }

    public function clock_in_out(Request $request)
    {
        if($request->type == 'in') {
            
            $found_job = Jobs::leftJoin('job_assignee as ja', 'ja.job_id', '=', 'jobs.id')
            ->where('ja.assigned_id', Auth::user()->id)
            ->whereDate('start_date_time', '<=', Carbon::now()->toDateString())
            ->whereDate('end_date_time', '>=', Carbon::now()->toDateString())
            ->selectRaw('jobs.id')
            ->first();

            if($found_job) {
                $tl = new TimeLog();
                $tl->job_id = $found_job->id;
                $tl->assigned_id = Auth::user()->id;
                $tl->date = Carbon::now()->toDateString();
                $tl->start_time = Carbon::now()->toTimeString();
                $tl->save(); 
            } else {
                return response()->json(['error'=>'No job assigned for you at the moment. Contact admin.']);
            }
            
        } else if($request->type == 'out'){
            $found_tl = TimeLog::where('assigned_id', Auth::user()->id)->whereNull('end_time')->where('job_id', $request->job_id)->first();

            if( $request->lunch_break == null ){
                return response()->json(['error'=>'Lunch break should have a value!']);
            } else if($found_tl && ($request->lunch_break == "1" || $request->lunch_break == "0" )) {

                if($request->signed) {
                    $folderPath = public_path('signature/');
                    $image_parts = explode(";base64,", $request->signed);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    $signature = uniqid() . '.'.$image_type;
                    $file = $folderPath . $signature;
                    file_put_contents($file, $image_base64);
                    $found_tl->signature = $signature;
                }
            
                if($request->authorized) {
                    $found_tl->authorized = $request->authorized;
                }
           
                $found_tl->lunch_break = $request->lunch_break;
                $found_tl->end_time = Carbon::now()->toTimeString();
                $found_tl->save();
            } 
            
        } else {
            if($request->lunch_break == "1" || $request->lunch_break == "0" ) {
                $found_tl = TimeLog::where('assigned_id', Auth::user()->id)
                    ->whereNull('end_time')
                    ->where('job_id', $request->job_id)
                    ->first();

                if($request->signed) {
                    $folderPath = public_path('signature/');
                    $image_parts = explode(";base64,", $request->signed);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    $signature = uniqid() . '.'.$image_type;
                    $file = $folderPath . $signature;
                    file_put_contents($file, $image_base64);
                    $found_tl->signature = $signature;
                }
                
                if($request->authorized) {
                    $found_tl->authorized = $request->authorized;
                }
               
                $found_tl->lunch_break = $request->lunch_break;
                $found_tl->save();
            } else {
                return response()->json(['error'=>'Lunch break should have a value!']);
            }
            
        }

        return response()->json(['success'=>'Log saved successfully.']);
    }

    public function edit($id)
    {
        $jobs = TimeLog::find($id);
        return response()->json($jobs);
    }

    public function destroy($id)
    {
        TimeLog::find($id)->delete();

        return response()->json(['success'=>'Log deleted successfully.']);
    }

    public function timesheet(Request $request)
    {
        $data = DB::table('time_logs as tl')
            ->leftJoin('jobs as j', 'tl.job_id', '=', 'j.id')
            ->leftJoin('clients as c', 'j.client_id', '=', 'c.id')
            ->leftJoin('job_assignee as ja', 'j.id', '=', 'ja.job_id')
            ->leftJoin('users as u', 'u.id', '=', 'ja.assigned_id')
            ->whereNotNull('tl.end_time')
            ->whereNotNull('ja.assigned_id')
            ->whereNotNull('j.id');
        if(Auth::user()->roles != 'admin') {
            $data = $data->where('ja.assigned_id', Auth::user()->id);
        }

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

        if ($request->user_type == 'full-timer' || $request->user_type == 'subcontractor') {
            $data = $data->where('u.roles', $request->user_type);
        }

        $data = $data->selectRaw('j.id, j.address, ja.job_title, ja.assigned_id, u.name, tl.job_id, tl.start_time, tl.end_time, date, 
                client_id, company_name, u.rate_per_hour, u.ot_rate_per_hour, lunch_break')->orderBy('date');
        if ($request->ajax()) {
                
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
                        $total_mins = $start_time->diffInMinutes($end_time);
                        $total_hr = round($total_mins / 60, 2);
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
                        $total_mins = $start_time->diffInMinutes($end_time);
                        $total_hr = round($total_mins / 60, 2);
                        $pay = 0;
                        
                        if($row->lunch_break) {
                            $total_hr = $total_hr - .5;
                        }
                        
                        if($total_hr > 4 && $total_hr <= 8 ) {
                            $ot_pay=0;
                            $pay = $total_hr * $row->rate_per_hour;
                        } else if($total_hr > 0 && $total_hr <= 4) {
                            $total_hr = 4;
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
                        $total_mins = $start_time->diffInMinutes($end_time);
                        $total_hr = round($total_mins / 60, 2);
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
                        $total_mins = $start_time->diffInMinutes($end_time);
                        $total_hr = round($total_mins / 60, 2);
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
                            $total_hr = 4;
                            $pay = 4 * $row->rate_per_hour;
                            $total_amount = $pay;
                        }
                        
                        return $total_amount;
                    })
                    ->make(true);
        }
        $clients = Client::pluck('company_name', 'id');
        $assigned = User::whereIn('roles', ['subcontractor', 'full-timer'])->pluck('name', 'id');

        $jobs = Jobs::pluck('address', 'id');
        if($request->user_type == "full-timer")
            $filter_assigned = User::where('roles', 'full-timer')->selectRaw('name, id')->get();
        else
            $filter_assigned = User::where('roles', 'subcontractor')->selectRaw('name, id')->get();

        return view('timelogs.timesheet', compact('clients', 'jobs', 'filter_assigned'));

    }
}
