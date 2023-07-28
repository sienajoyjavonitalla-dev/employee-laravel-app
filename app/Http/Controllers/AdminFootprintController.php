<?php

namespace App\Http\Controllers;

use App\Models\AdminFootprint;
use Illuminate\Http\Request;
use App\Mail\JobCancelled;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use DataTables;

class AdminFootprintController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if( $request->ajax() )
        {
            $data = AdminFootprint::latest()->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->make(true);
        }      

        return view('logs.footprint');
    }

    public function testPostmark( )
    {
        $user = USER::find(1);

        Mail::to($user)->send(new JobCancelled(
            $user->id,
            $user->name
        )); 
    }
}
