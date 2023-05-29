<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobs;
use DataTables;
use App\Models\Client;
use App\Models\User;

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
                        return $client->client_name;
                    })
                    ->addColumn('assigned', function($row){
                        $user = User::where('id', $row->employee_id)->first();
                        return $user->name;
                    })
                    ->addColumn('action', function($row){
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editJob">Edit</a>';
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteJob">Delete</a>';

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        $clients = Client::pluck('client_name', 'id');
        $assigned = User::whereIn('roles', ['subcontractor', 'full timer'])->pluck('name', 'id');

        return view('jobs.index', compact('clients', 'assigned'));

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
        Jobs::updateOrCreate([
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
            'start_date_time' => date_format(date_create($request->start_date_time),"Y-m-d"),
            'end_date_time' => date_format(date_create($request->end_date_time),"Y-m-d")
        ]);

        return response()->json(['success'=>'Job saved successfully.']);
    }

    public function edit($id)
    {
        $jobs = Jobs::find($id);
        return response()->json($jobs);
    }

    public function destroy($id)
    {
        Jobs::find($id)->delete();

        return response()->json(['success'=>'Job deleted successfully.']);
    }
}
