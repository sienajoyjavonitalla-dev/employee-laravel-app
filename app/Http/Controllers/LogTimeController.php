<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Models\TimeLog;
use Illuminate\Support\Facades\Auth;

class LogTimeController extends Controller
{
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
        
        return view('timelogs.timeclock', compact('clock_in'));
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
