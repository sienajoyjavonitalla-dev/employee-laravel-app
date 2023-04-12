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

        $jobs = Jobs::create($request->all());

        // session()->flash('message', 'Job is added successfully!');
        if ($jobs) {
            return back()->with('success', 'Success! Job created');
        }
        else {
            return back()->with('failed', 'Failed! Job not created');
        }
    }
}
