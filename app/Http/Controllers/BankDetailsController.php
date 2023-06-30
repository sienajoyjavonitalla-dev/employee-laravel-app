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
use DB;
use App\Models\Client;
use App\Models\BankDetails;

class BankDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BankDetails::latest()->get();

            return Datatables::of($data)
                    ->addIndexColumn()
                    
                    ->addColumn('action', function($row){
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editBank">Edit</a>';
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteBank">Delete</a>';

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('bank.index');

    }
    public function edit($id)
    {
        $bank = BankDetails::find($id);
        return response()->json($bank);
    }
    public function store(Request $request)
    {
        
        BankDetails::updateOrCreate([
            'id' => $request->id
        ],
        [
            'bsb_no' => $request->bsb_no,
            'acct_no' => $request->acct_no
        ]);

        return response()->json(['success'=>'Bank Details saved successfully.']);
    }

    public function destroy($id)
    {
        BankDetails::find($id)->delete();

        return response()->json(['success'=>'Bank Details deleted successfully.']);
    }
}
