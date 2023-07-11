<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\JobAssignee;
use App\Models\AdminFootprint;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if(Auth::user()->roles != 'admin')
        {
            return redirect('/timeclock');
        }

        if ($request->ajax()) {

            $data = User::latest()->get();

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editUser">Edit</a>';
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteUser">Delete</a>';

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('users.index');

    }

    public function show(User $id)
    {
        return view('users.index', ['Users' => $id]);
    }

    public function create()
    {
        return view('users.create', ['users' => new User()]);
    }

    public function store(Request $request)
    {
        DB::transaction(function() use($request) {
            $footprint = "";

            $adminUser = Auth::user();

            $user = null;
            $action_type = "";

            if($request->user_id) {
                
                $user = User::where('id', $request->user_id)->first();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->roles = $request->roles;

                if($request->password != null){
                    $user->password = Hash::make($request->password);
                }

                $user->rate_per_hour = $request->rate_per_hour;
                $user->ot_rate_per_hour = $request->ot_rate_per_hour;
                $user->save();

                $action_type = "update";
                $footprint = "$adminUser->name updated user $user->name";
            } else {
                $user = User::create(
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'roles' => $request->roles,
                    'rate_per_hour' => $request->rate_per_hour,
                    'ot_rate_per_hour' => $request->ot_rate_per_hour
                ]);

                $action_type = "create";
                $footprint = "$adminUser->name created user $user->name";
            }

            AdminFootprint::create([
                'user_id' => $adminUser->id,
                'action_type' => $action_type,
                'entity_id' => $user->id,
                'entity' => $user->name,
                'description' => $footprint
            ]);
        });

        return response()->json(['success'=>'User saved successfully.']);
    }

    public function edit($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        $jobAssignees = JobAssignee::where('assigned_id', $user->id)->get();

        $response = [];

        if($jobAssignees->count() > 0)
        {
            return response('User still assigned to a job.', 400);
        }
        else
        {   
            DB::transaction(function() use ($user) {

                $adminUser = Auth::user();

                $footprint = "$adminUser->name deleted $user->name";

                $user->delete();

                AdminFootprint::create([
                    'user_id' => $adminUser->id,
                    'action_type' => 'delete',
                    'entity_id' => $user->id,
                    'entity' => $user->name,
                    'description' => $footprint
                ]);

                return response('User deleted successfully.', 200);
            });            
        }
    }


    public function how_tos() {
        return view('users.how_tos');
    }
}
