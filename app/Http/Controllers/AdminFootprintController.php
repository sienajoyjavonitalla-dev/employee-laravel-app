<?php

namespace App\Http\Controllers;

use App\Models\AdminFootprint;
use Illuminate\Http\Request;
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

    public function show(AdminFootprint $id)
    {

    }

    public function store(Request $request)
    {
 
    }
}
