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

    }

    public function show(AdminFootprint $id)
    {

    }

    public function create()
    {

    }

    public function store(Request $request)
    {
 
    }

    public function edit($id)
    {

    }

    public function destroy($id)
    {

    }
}
