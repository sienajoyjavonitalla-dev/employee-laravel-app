<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobs;
use App\Models\Appointment;
use DataTables;
use Illuminate\Support\Facades\DB;

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
                    ->addColumn('action', function($row){
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editJob">Edit</a>';
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteJob">Delete</a>';

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('jobs.index');

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
                    'end_date_time' => $request->end_date_time
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
