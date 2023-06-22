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
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteTime">Delete</a>';

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('timelogs.index');

    }

    public function show(Jobs $id)
    {

        return view('timelogs.index', ['TimeLogs' => $id]);
    }

    public function timeclock()
    {
        $clock_in = TimeLog::where('assigned_id', Auth::user()->id)->orderBy('created_at', 'desc')->first();

        $job = Jobs::leftJoin('clients as clients', 'jobs.client_id', '=', 'clients.id')
            ->leftJoin('job_assignee as ja', 'ja.job_id', '=', 'jobs.id')
            ->where('ja.assigned_id', Auth::user()->id)
            ->whereDate('start_date_time', '<=', Carbon::now()->toDateString())
            ->whereDate('end_date_time', '>=', Carbon::now()->toDateString())
            ->selectRaw('jobs.address, clients.company_name, jobs.id, ja.job_title as title, jobs.start_date_time, jobs.end_date_time, jobs.start_time, jobs.end_time')
            ->first();
        return view('timelogs.timeclock', compact('clock_in', 'job'));
    }

    public function store(Request $request)
    {
        if($request->file('timesheet')){
            $file= $request->file('timesheet');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('img'), $filename);
            // $data['image']= $filename;
        } else {
            $tl=TimeLog::where('id', $request->id)->first();
            $filename = $tl->timesheet;
        }
        TimeLog::updateOrCreate([
            'id' => $request->id
        ],
        [
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
            
        } else {
            $found_tl = TimeLog::where('assigned_id', Auth::user()->id)->whereNull('end_time')->first();
            $found_tl->end_time = Carbon::now()->toTimeString();
            $found_tl->save();
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
}
