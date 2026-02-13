<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobs;
use App\Models\Appointment;
use App\Models\AdminFootprint;
use DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\User;
use App\Models\JobAssignee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\JobCancelled;
use App\Mail\JobAssigned;

class JobsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {

            if ($request->name == 'list') {
                $data = DB::table('jobs as j')
                    ->leftJoin('invoices as i', 'i.job_id', '=', 'j.id')
                    ->leftJoin('clients as c', 'c.id', '=', 'j.client_id')
                    ->selectRaw('j.id, j.client_id, j.status, j.address, j.start_date_time, j.end_date_time, i.invoice_id, i.invoice_url, c.company_name')
                    ->get();
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('invoice', function($row){
                        $inv = '';
                        if($row->invoice_id) {
                            $inv = "<a href='".$row->invoice_url."' target='_blank'> ".$row->invoice_id."</a>";
                        } else if($row->status == 'complete'){
                            $inv = '<a href="javascript:void(0)" data-compid="'.$row->client_id.'"  data-company="'.$row->company_name.'"  data-id="'.$row->id.'" data-toggle="tooltip" class="btn btn-secondary btn-xs generateBtn"><i class="fa-solid fa-gear"></i>Generate</a>';

                        }
                        return $inv;
                    })
                    ->addColumn('client', function($row){
                        $client = Client::where('id', $row->client_id)->first();
                        return $client->company_name ?? '';
                    })
                    ->addColumn('assigned', function($row){
                        $assigned = DB::table('job_assignee as ja')
                            ->leftJoin('users as u', 'ja.assigned_id', '=', 'u.id')
                            ->where('ja.job_id', $row->id)
                            ->select('u.name')
                            ->get();
                        $display = "";

                        if($assigned->count() > 0) {
                            $count=0;
                            foreach($assigned as $a) {
                                if($count > 0)
                                    $display .=', ';
    
                                $display .=$a->name;
                                $count++;
                            }

                        } 

                        if( Auth::user()->roles == 'admin')
                        {
                            $display .= '<a href="javascript:void(0)"  data-id="'.$row->id.'" data-toggle="tooltip" class="btn-xs assignBtn"><i class="fas fa-pen"></i></a>';
                        }
                        
                        return $display;
                    })
                    ->addColumn('stat_change', function($row){
                        $display = $row->status;
                        if($row->status == 'assigned')
                            $display .= '<a href="javascript:void(0)"  data-id="'.$row->id.'" data-toggle="tooltip" data-placement="bottom" title="Complete Job" class="btn-xs text-success completeBtn"><i class="fas fa-check"></i></a>';
                        return $display;
                    })
                    ->addColumn('action', function($row){
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editJob">Edit</a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteJob">Delete</a>';

                        return $btn;
                    })
                    ->rawColumns(['action', 'assigned', 'invoice', 'stat_change'])
                    ->make(true);
            } else if ($request->name == 'generate') {
                $data = DB::table('time_logs as tl')
                    ->leftJoin('jobs as jobs', 'job_id', '=', 'jobs.id')
                    ->leftJoin('clients as clients', 'jobs.client_id', '=', 'clients.id')
                    ->leftJoin('job_assignee as ja', function($join)
                    {
                        $join->on('tl.job_id', '=', 'ja.job_id');
                        $join->on('tl.assigned_id', '=', 'ja.assigned_id');
                    })
                    ->whereNotNull('tl.end_time');

                
                if ($request->filled('company_id')) {
                    $data = $data->where('client_id', $request->company_id);
                }

                if ($request->filled('job_id')) {
                    $data = $data->where('tl.job_id', $request->job_id);
                }

                $data = $data->selectRaw('jobs.id, ja.job_title, po_number, tl.assigned_id, tl.job_id, tl.start_time, tl.end_time, date, client_id, company_name, rate_per_hour, other_rate_per_hour, ot_rate_per_hour, lunch_break');
                // dd($data->toSql());
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('employee', function($row){
                        $qry = User::where('id', $row->assigned_id)->first();
                        $user = $qry->name;
                        return $user;
                    })
                    ->addColumn('hrs_worked', function($row){
                        $total_hr = 0;
                        $start_time = new Carbon($row->start_time);
                        $end_time =new Carbon($row->end_time);
                        $total_mins = $start_time->diffInMinutes($end_time);
                        $total_hr = round($total_mins / 60, 2);
                        if($row->lunch_break) {
                            $total_hr = $total_hr - .5;
                        }
                        return $total_hr > 0 ? $total_hr : 0;
                    })
                    ->addColumn('with_lunch', function($row){
                        return $row->lunch_break ? 'Yes':'No';
                    })
                    ->addColumn('pay', function($row){
                        $total_hr = 0;
                        $start_time = new Carbon($row->start_time);
                        $end_time =new Carbon($row->end_time);
                        $total_mins = $start_time->diffInMinutes($end_time);
                        $total_hr = round($total_mins / 60, 2);
                        $pay = 0;
                        
                        if($row->lunch_break) {
                            $total_hr = $total_hr - .5;
                        }
                        
                        $rate = strtolower($row->job_title) == 'operator' ? $row->other_rate_per_hour : $row->rate_per_hour;

                        $isWeekend = false;
                        if($row->date) {
                            $day = Carbon::createFromFormat('Y-m-d', $row->date );
                            $isWeekend = $day->isWeekend();
                            if($isWeekend) {
                                $rate = $row->ot_rate_per_hour;
                            }
                        }
                        if($total_hr > 4 && $total_hr <= 8 ) {
                            $ot_pay=0;
                            $pay = $total_hr * $rate;
                        } else if($total_hr > 0 && $total_hr <= 4) {
                            $total_hr = 4;
                            $pay = 4 * $rate;
                        } else if($total_hr > 8) {
                            $total_hr = 8;
                            $pay = 8 * $rate;
                        }
                        $pay = number_format((float)$pay, 2, '.', '');
                        
                        return $pay;
                    })
                    ->addColumn('ot_pay', function($row){
                        $total_hr = 0;
                        $start_time = new Carbon($row->start_time);
                        $end_time =new Carbon($row->end_time);
                        $total_mins = $start_time->diffInMinutes($end_time);
                        $total_hr = round($total_mins / 60, 2);
                        $ot_pay = 0;
                        if($row->lunch_break) {
                            $total_hr = $total_hr - .5;
                        }
                        if($total_hr > 8) {
                            $ot_hours= $total_hr - 8;
                            $ot_pay = $ot_hours * $row->ot_rate_per_hour;
                        }
                        $ot_pay = number_format((float)$ot_pay, 2, '.', '');

                        return round($ot_pay, 2);
                    })
                    ->addColumn('total', function($row){
                        $total_hr = 0;
                        $start_time = new Carbon($row->start_time);
                        $end_time =new Carbon($row->end_time);
                        $total_mins = $start_time->diffInMinutes($end_time);
                        $total_hr = round($total_mins / 60, 2);
                        $total_amount=0;
                        if($row->lunch_break) {
                            $total_hr = $total_hr - .5;
                        }
                        $rate = strtolower($row->job_title) == 'operator' ? $row->other_rate_per_hour : $row->rate_per_hour;
                        
                        $isWeekend = false;
                        if($row->date) {
                            $day = Carbon::createFromFormat('Y-m-d', $row->date );
                            $isWeekend = $day->isWeekend();
                            if($isWeekend) {
                                $rate = $row->ot_rate_per_hour;
                            }
                        }    
                        if($total_hr > 4) {
                            $ot_pay=0;
                            $pay = $total_hr * $rate;
                            if($total_hr > 8) {
                                $ot_hours= $total_hr - 8;
                                $ot_pay = $ot_hours * $row->ot_rate_per_hour;
                                $total_hr = 8;
                                $pay = $total_hr * $rate;

                            }
                            $total_amount = $ot_pay + $pay;
                        } else if($total_hr > 0 && $total_hr <= 4) {
                            $total_hr = 4;
                            $pay = 4 * $rate;
                            $total_amount = $pay;
                        }
                        $total_amount = number_format((float)$total_amount, 2, '.', '');
                        
                        return $total_amount;
                    })
                    ->make(true);
            } 
        }

        $clients = Client::orderBy('company_name')->pluck('company_name', 'id');
        $assigned = User::whereIn('roles', ['subcontractor', 'full-timer'])->orderBy('name')->pluck('name', 'id');

        return view('jobs.index', compact('clients', 'assigned'));

    }

    public function assigned_index(Request $request, Jobs $job)
    {
        if ($request->ajax()) {

            $data = JobAssignee::leftJoin('users as u', 'job_assignee.assigned_id', '=', 'u.id')
                ->where('job_id', $job->id)
                ->selectRaw('job_assignee.id, name, job_title')
                ->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteJobAssign">Delete</a>';

                        return $btn;
                    })
                    ->rawColumns(['action', 'assigned'])
                    ->make(true);
        }

        $clients = Client::orderBy('company_name')->pluck('company_name', 'id');
        $assigned = User::whereIn('roles', ['subcontractor', 'full-timer'])->orderBy('name')->pluck('name', 'id');

        return view('jobs.index', compact('clients', 'assigned'));

    }

    public function user_jobs(Request $request)
    {
        if ($request->ajax()) {

            $data = DB::table('jobs as j')
                ->leftJoin('job_assignee as ja', 'ja.job_id', '=', 'j.id')
                ->leftJoin('clients as c', 'c.id', '=', 'j.client_id')
                ->where('ja.assigned_id', Auth::user()->id)
                ->selectRaw('j.id, j.client_id, j.status, j.address, j.start_date_time, j.end_date_time, c.company_name')
                ->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('client', function($row){
                    $client = Client::where('id', $row->client_id)->first();
                    return $client->company_name ?? '';
                })
                ->addColumn('assigned', function($row){
                    $assigned = DB::table('job_assignee as ja')
                        ->leftJoin('users as u', 'ja.assigned_id', '=', 'u.id')
                        ->where('ja.job_id', $row->id)
                        ->select('u.name')
                        ->get();
                    $display = "";

                    if($assigned->count() > 0) {
                        $count=0;
                        foreach($assigned as $a) {
                            if($count > 0)
                                $display .=', ';

                            $display .=$a->name;
                            $count++;
                        }
                    } 

                    return $display;
                })
                
                ->rawColumns(['assigned'])
                ->make(true);
        }

        return view('jobs.user_jobs');

    }

    public function assigned_delete(Request $request, JobAssignee $assign)
    {
        DB::transaction(function() use ($request, $assign){

            $d = JobAssignee::find($assign->id);
            $job_id = $d->job_id;
            $d->delete();
    
            $ja = JobAssignee::where('job_id', $job_id)->get();
            $job = Jobs::where('id', $job_id)->first();
            $action_user = User::find($assign->assigned_id);
            $user = Auth::user();
    
            if($ja->count() == 0)
            {    
                $job->status = 'open';
                $job->save();
            }

            $footprint = "$user->name unassiged $action_user->name from job #$job->id";

            AdminFootprint::create([
                'user_id' => $user->id,
                'action_type' => 'unassign',
                'entity_id' => $action_user->id,
                'entity' => $action_user->name,
                'entity_target_id' => $job->id,
                'entity_target' => $d->job_title,
                'description' => $footprint
            ]);
        });

        

        return response()->json(['success'=>'Deleted successfully.']);
    }

    public function show(Jobs $id)
    {
        return view('jobs.index', ['Jobs' => $id]);
    }

    public function create()
    {
        return view('jobs.create', ['jobs' => new Jobs()]);
    }

    public function store(Request $request)
    {
        DB::transaction(function() use ($request) {

            $filename = '';
            $signature = '';
            if($request->file('timesheet')){
                $path = public_path().'/job_timesheets/'.$request->job_id;
                
                //delete the old file
                if($request->id) {
                        $j=Jobs::where('id', $request->id)->first();
                    if($j->timesheet) {
                        File::delete($path.'/'.$j->timesheet);
                    }

                }
                //make a directory for the timesheets and save
                if (!file_exists($path)) {
                    mkdir($path, 0775, true);
                }
                $file= $request->file('timesheet');
                $filename= date('YmdHi').$file->getClientOriginalName();
                $file->move($path, $filename);

            } else {
                if($request->id) {
                    $j=Jobs::where('id', $request->id)->first();
                    $filename = $j->timesheet;
                }
            }

            $job_check = $request->job_id;

            $job = Jobs::updateOrCreate(
                [
                    'id' => $request->job_id
                ],
                [
                    'client_id' => $request->client_id,
                    'employee_id' => $request->employee_id,
                    'po_number' => $request->po_number,
                    'description' => $request->description,
                    'address' => $request->address,
                    'start_date_time' => $request->start_date_time,
                    'end_date_time' => $request->end_date_time,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'timesheet' => $filename,
                    'type' => $request->type
                ]
            );

            $user = Auth::user();

            $footprint = "";
            $action = "";

            if( $job_check == null )
            {
                $footprint = "$user->name created job #$job->id";
                $action = "create";
            } else {
                $footprint = "$user->name updated job #$job->id";
                $action = "update";
            }

            AdminFootprint::create([
                'user_id' => $user->id,
                'action_type' => $action,
                'entity_id' => $job->id,
                'description' => $footprint
            ]);
        },2);

        return response()->json(['success'=>'Job saved successfully.']);
    }

    public function store_assigned(Request $request)
    {
        if( $request->assigned_id != null )
        {
            DB::transaction( function() use ($request){
                $job = Jobs::where('id', $request->job_assignee_id)->first();
                $job->status = 'assigned';
                $job->save();
        
                $assigned = JobAssignee::where('job_id', $request->job_assignee_id)->get()->count();
                
                $assigned =JobAssignee::updateOrCreate(
                    ['job_id' => $request->job_assignee_id,
                    'assigned_id' => (int)$request->assigned_id],
                    [
                        'job_id' => $request->job_assignee_id,
                        'assigned_id' => (int)$request->assigned_id,
                        'job_title' => $request->job_title
                    ]
                );
    
                // $user = User::find($assigned->assigned_id);
    
                // Mail::to($user)->send(new JobAssigned(
                //     $job->id,
                //     $request->job_title,
                //     $user->name
                // ));    

                $user = Auth::user();
                $action_user = User::find($request->assigned_id);

                $footprint = "$user->name assigned $action_user->name to job #$job->id";

                AdminFootprint::create([
                    'user_id' => $user->id,
                    'action_type' => 'assign',
                    'entity_id' => $action_user->id,
                    'entity' => $action_user->name,
                    'entity_target_id' => $job->id,
                    'entity_target' => $job->title,
                    'description' => $footprint
                ]);
            }); 
        }

        return response()->json(['success'=>'Job Assigned successfully.']);

    }

    public function complete_job(Request $request)
    {
        $job = Jobs::where('id', $request->job_id)->first();
        $job->status = 'complete';
        $job->save();

        $user = Auth::user();

        $footprint = "$user->name changed job #$job->id status to Complete";

        AdminFootprint::create([
            'user_id' => $user->id,
            'action_type' => 'update',
            'entity_id' => $job->id,
            'description' => $footprint
        ]);

        return response()->json(['success'=>'Job is set to Completed successfully.']);
    }

    public function generateInvoice(Request $req) {
        $job_id = $req->job_id;
        $client_id = $req->client_id;

        $timelogs = DB::table('time_logs as tl')->leftJoin('jobs as jobs', 'job_id', '=', 'jobs.id')
                    ->leftJoin('clients as clients', 'jobs.client_id', '=', 'clients.id')
                    ->leftJoin('users as u', 'tl.assigned_id', '=', 'u.id')
                    ->leftJoin('job_assignee as ja', function($join)
                    {
                        $join->on('tl.job_id', '=', 'ja.job_id');
                        $join->on('tl.assigned_id', '=', 'ja.assigned_id');
                    })
                    ->where('client_id', $client_id)
                    ->where('tl.job_id', $job_id)
                    ->whereNotNull('tl.end_time')
                    ->selectRaw('u.name, clients.travel_allowance, date, jobs.id, ja.job_title, po_number, 
                        clients.address, tl.assigned_id, tl.job_id, tl.start_time, tl.end_time, tl.date, 
                        client_id, client_name, company_name, lunch_break, clients.rate_per_hour, clients.other_rate_per_hour, clients.ot_rate_per_hour, 
                        TIMESTAMPDIFF(HOUR, tl.start_time, tl.end_time), jobs.address as job_address')
                    ->get();
        if($timelogs->count() == 0) {
            return response()->json(['error'=>'No Timelogs, cannot generate!']);

        }

        $client_details = Client::where('id', $client_id)->first();
        $job_details = Jobs::where('id', $job_id)->first();

        $line_items = [];
        $count_travel = 0;

        foreach($timelogs as $tl) {
            $total_hr = 0;
            $total_amount = 0;
            $ot_hours = 0;
            $start_time = new Carbon($tl->start_time);
            $end_time =new Carbon($tl->end_time);
            $total_mins = $start_time->diffInMinutes($end_time);
            $total_hr = round($total_mins / 60, 2);
            if($tl->lunch_break) {
                $total_hr = $total_hr - .5;
            }
            // $rate = $tl->rate_per_hour;
            $rate = strtolower($tl->job_title) == 'operator' ? $tl->other_rate_per_hour : $tl->rate_per_hour;

            $isWeekend = false;
            if($tl->date) {
                $day = Carbon::createFromFormat('Y-m-d', $tl->date );
                $isWeekend = $day->isWeekend();
                if($isWeekend) {
                    $rate = $tl->ot_rate_per_hour;
                }
            }
            if($total_hr > 4) {
                $ot_pay=0;
                $pay = $total_hr * $rate;
                if($total_hr > 8) {
                    $ot_hours= $total_hr - 8;
                    $ot_pay = $ot_hours * $tl->ot_rate_per_hour;
                    $total_hr = 8;
                    $pay = $total_hr * $rate;
                }
                $total_amount = $pay;
            } else if($total_hr > 0 && $total_hr <= 4) {
                $pay = 4 * $rate;
                $total_hr = 4;
                $total_amount = $pay;
            }

            if($total_hr > 0) {
                $count_travel = $count_travel + 1;

                array_push($line_items, (object)[
                    'Description'=> $tl->date . ' ' . $tl->name,
                    'Quantity'=> $total_hr,
                    'UnitAmount'=> $rate,
                    'AccountCode'=> '200',
                    'TaxType'=> 'OUTPUT',
                    'LineAmount'=> $total_amount
                ]);
            }
            
            //ot pay add line item
            if($ot_hours > 0) {
                array_push($line_items, (object)[
                    'Description'=> $tl->date . ' ' . $tl->name .' Overtime',
                    'Quantity'=> $ot_hours,
                    'UnitAmount'=> $tl->ot_rate_per_hour,
                    'AccountCode'=> '200',
                    'TaxType'=> 'OUTPUT',
                    'LineAmount'=> $ot_pay
                ]);
            }
            
        } 

        if($count_travel > 0) {
            array_push($line_items, (object)[
                'Description'=> 'Travel Allowance',
                'Quantity'=> $count_travel,
                'UnitAmount'=> $client_details->travel_allowance,
                'AccountCode'=> '200',
                'TaxType'=> 'OUTPUT',
                'LineAmount'=> ($count_travel * $client_details->travel_allowance)
            ]);
        }

        $subtotal = 0;
        foreach ($line_items as $li) {
            $subtotal += $li->LineAmount;
        }
        $local_invoice_id = 'local-' . $job_id . '-' . time();
        $date_string = Carbon::today()->toDateString();

        Invoice::updateOrCreate(
            ['invoice_id' => $local_invoice_id],
            [
                'job_id' => $job_id,
                'client_id' => $client_id,
                'type' => 'ACCREC',
                'invoice_number' => 'INV-' . $job_id . '-' . time(),
                'amount_due' => $subtotal,
                'amount_paid' => 0,
                'date_string' => $date_string,
                'duedate_string' => $date_string,
                'status' => 'AUTHORISED',
                'subtotal' => $subtotal,
                'TotalTax' => 0,
                'Total' => $subtotal,
                'currency_code' => 'AUD',
                'updated_date_utc' => now()->toIso8601String(),
                'fully_paid_date_utc' => ''
            ]
        );

        foreach ($line_items as $idx => $l) {
            LineItem::create([
                'job_id' => $job_id,
                'invoice_id' => $local_invoice_id,
                'LineItemID' => $local_invoice_id . '-line-' . $idx,
                'Description' => $l->Description,
                'UnitAmount' => $l->UnitAmount,
                'TaxType' => $l->TaxType ?? '',
                'TaxAmount' => $l->TaxAmount ?? 0,
                'LineAmount' => $l->LineAmount,
                'Quantity' => $l->Quantity
            ]);
        }

        $user = Auth::user();
        $footprint = "$user->name generated an invoice for job #$job_details->id";
        AdminFootprint::create([
            'user_id' => $user->id,
            'action_type' => 'create',
            'entity_id' => $job_details->id,
            'description' => $footprint
        ]);

        return response()->json(['success'=>'Invoice created successfully.']);
    }

    public function sendAttachments($job_id, $invoice_id, $job_details) {
        // Xero removed: attachment upload disabled for demo
    }

    public function sendRequestWithRarFile()
    {
        // Xero removed: no-op for demo
    }

    public function edit($id)
    {
        $jobs = Jobs::find($id);
        return response()->json($jobs);
    }

    public function destroy($id)
    {

        DB::transaction(function() use($id) {

            $jobAssignees = JobAssignee::where('job_id', $id)->get();

            foreach ($jobAssignees as $jobAssignee) {
                $jobAssignee->delete();
                $user = User::find($jobAssignee->assigned_id);

                // Mail::to($user)->send(new JobCancelled(
                //     $id,
                //     $user->name
                // ));       
            }

            $job = Jobs::find($id);
            $job->delete();

            $user = Auth::user();

            $footprint = "$user->name deleted job #$job->id";

            AdminFootprint::create([
                'user_id' => $user->id,
                'action_type' => 'delete',
                'entity_id' => $job->id,
                'entity' => $job->title,
                'description' => $footprint
            ]);

        },2);        

        return response()->json([
            'id' => $id,
            'success'=>'Job deleted successfully.'
        ]);
    }
}
