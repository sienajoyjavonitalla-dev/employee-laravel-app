<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserFiles;
use App\Models\JobAssignee;
use App\Models\AdminFootprint;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if(Auth::user()->roles != 'admin' && Auth::user()->roles != 'subadmin')
        {
            return redirect('/timeclock');
        }

        if ($request->ajax()) {

            $data = User::latest()->get();

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = '<a href="javascript:void(0)"  data-id="'.$row->id.'" data-toggle="tooltip" class="btn btn-info btn-sm manageFiles"><i class="fas fa-file"></i>  Files</a>';
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editUser"><i class="fas fa-pen"></i>  Edit</a>';
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteUser"><i class="fas fa-trash"></i>  Delete</a>';

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
                $user->abn = $request->abn;
                $user->email = $request->email;
                $user->roles = $request->roles;

                if($request->password != null){
                    $user->password = Hash::make($request->password);
                }

                $user->rate_per_hour = $request->rate_per_hour;
                $user->other_rate_per_hour = $request->other_rate_per_hour;
                $user->ot_rate_per_hour = $request->ot_rate_per_hour;
                $user->other_ot_rate_per_hour = $request->other_ot_rate_per_hour;
                $user->travel_allowance = $request->travel_allowance;
                $user->gst = $request->gst;
                $user->save();

                $action_type = "update";
                $footprint = "$adminUser->name updated user $user->name";
            } else {
                $user = User::create(
                [
                    'name' => $request->name,
                    'abn' => $request->abn,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'roles' => $request->roles,
                    'rate_per_hour' => $request->rate_per_hour,
                    'other_rate_per_hour' => $request->other_rate_per_hour,
                    'ot_rate_per_hour' => $request->ot_rate_per_hour,
                    'other_ot_rate_per_hour' => $request->other_ot_rate_per_hour,
                    'travel_allowance' => $request->travel_allowance,
                    'gst' => $request->gst

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

    public function store_user_files(Request $request)
    {
        if($request->hasFile('image_path')) {
            $path = public_path().'/user_files';
           
            //make a directory for the timesheets and save
             if (!file_exists($path)) {
                 mkdir($path, 0775, true);
             }
            $file= $request->file('image_path');
             
            $originalFilename = $file->getClientOriginalName();

            // Remove spaces from the original filename
            $originalFilenameWithoutSpaces = str_replace(' ', '', $originalFilename);
             
            $userfilename = date('YmdHis') . $originalFilenameWithoutSpaces;
            $file->move($path, $userfilename);
        } 
        
        // Save the record to the database
        UserFiles::create([
            'user_id' => $request->input('file_user_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'image_path' => $userfilename
        ]);

        $user = Auth::user();
        $action_user = User::find($request->input('file_user_id'));
        $footprint = "$user->name added file $userfilename to $action_user->name";

        AdminFootprint::create([
            'user_id' => $user->id,
            'action_type' => 'add file',
            'entity_id' => $action_user->id,
            'entity' => $action_user->name,
            'description' => $footprint
        ]);
        
        return response()->json(['success'=>'File added successfully.']);

    }

    public function user_files_index(Request $request, User $user)
    {
        if ($request->ajax()) {

            $data = UserFiles::leftJoin('users as u', 'user_files.user_id', '=', 'u.id')
                ->where('user_id', $user->id)
                ->selectRaw('user_files.id, user_files.name, description, image_path')
                ->get();

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('image_path', function($row){

                        return "<a href=".url('user_files/'.$row->image_path)." target='_blank'><img src=".url('user_files/'.$row->image_path)." width='50' height='50'></a>";
                    })
                    ->addColumn('action', function($row){
                        $btn = ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteUserFile">Delete</a>';

                        return $btn;
                    })
                    ->rawColumns(['action', 'image_path'])
                    ->make(true);
        }

        return view('users.index');

    }

    public function user_files_delete(Request $request, UserFiles $file)
    {

        $d = UserFiles::find($file->id);
        $d->delete();
        
        File::delete('user_files/'.$d->image_path);
       
        $action_user = User::find($d->user_id);
        $user = Auth::user();

        $footprint = "$user->name deleted the file of $action_user->name with name $d->name";

        AdminFootprint::create([
            'user_id' => $user->id,
            'action_type' => 'delete',
            'entity_id' => $action_user->id,
            'entity' => $action_user->name,
            'description' => $footprint
        ]);

        return response()->json(['success'=>'Deleted successfully.']);
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
