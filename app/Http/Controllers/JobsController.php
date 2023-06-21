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

class JobsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Jobs::latest()->get();

            return Datatables::of($data)
                    ->addIndexColumn()
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
                    ->rawColumns(['action', 'assigned'])
                    ->make(true);
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
                    'title' => $request->title,
                    'client_id' => $request->client_id,
                    'employee_id' => $request->employee_id,
                    'status' => $status,
                    'po_number' => $request->po_number,
                    'description' => $request->description,
                    'address' => $request->address,
                    'start_date_time' => $request->start_date_time,
                    'end_date_time' => $request->end_date_time,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time
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
