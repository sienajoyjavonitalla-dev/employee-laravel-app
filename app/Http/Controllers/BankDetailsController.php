<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Models\TimeLog;
use App\Models\Jobs;
use App\Models\JobAssignee;
use App\Models\AdminFootprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
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
        DB::transaction(function() use ($request) {
            $check = $request->id;
            $action_type = "";
    
            $user = Auth::user();
    
            $bankDetails = BankDetails::updateOrCreate([
                'id' => $request->id
            ],
            [
                'bsb_no' => $request->bsb_no,
                'acct_no' => $request->acct_no,
                'name' => $request->account_name
            ]);
    
            if( $check == null){
                $action_type = "create";
                $footprint = "$user->name created bank details for $bankDetails->name";
            } else {
                $action_type = "update";
                $footprint = "$user->name updated bank details for $bankDetails->name";
            }
    
            AdminFootprint::create([
                'user_id' => $user->id,
                'action_type' => $action_type,
                'entity_id' => $bankDetails->id,
                'entity' => $bankDetails->name,
                'description' => $footprint
            ]);
        });

        return response()->json(['success'=>'Bank Details saved successfully.']);
    }

    public function destroy($id)
    {
        DB::transaction(function() use ($id) {
            $user = Auth::user();

            $bankDetails = BankDetails::find($id);
    
            $footprint = "$user->name deleted bank $bankDetails->name from the records";
    
            AdminFootprint::create([
                'user_id' => $user->id,
                'action_type' => 'delete',
                'entity_id' => $bankDetails->id,
                'entity' => $bankDetails->name,
                'description' => $footprint
            ]);

            $bankDetails->delete();
        });

        return response()->json(['success'=>'Bank Details deleted successfully.']);
    }
}
