<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobs;

class JobsController extends Controller
{
    public function index()
    {
        $games = Jobs::all();
        return view('jobs.index', ['jobs' => $jobs]);
    }

    public function show(Jobs $id)
    {
        return view('jobs.show', ['Jobs' => $id]);
    }

    public function create()
    {
        return view('jobs.create');
    }

    public function store(Request $request)

    {

        $this->validate($request, [

            'title' => 'required',

            'client_id' => 'required',

        ]);

        Jobs::create($request->all());

        // return redirect()->route('jobs.index')->with('success','Jobs created successfully');
        // return response()->json(['success'=>true, 'message' => 'Successfull!']);
        
        // return redirect()->back()->with('alert', 'Successfully added Job!');

        return response()->json([
            'alert' => 'success'
        ]);
    }
}
