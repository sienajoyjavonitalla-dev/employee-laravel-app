<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Jobs;
use DataTables;
use Illuminate\Support\Facades\DB;

class ClientsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Client::latest()->get();

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('complete_address', function($row){
                        $comp = $row->POBOX_AddressLine1 . ', ' . $row->POBOX_City . ', ' .$row->POBOX_Region . ', ' .$row->POBOX_Country;
                        return $comp;
                    })
                    ->addColumn('action', function($row){
                        $btn ='<a href="'.route('client.show-invoices', $row->id).'" data-id="'.$row->id.'" data-toggle="tooltip" class="btn btn-light btn-sm" target="_blank"></i>Invoice</a>';
                        $btn .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editClient">Edit</a>';
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteClient">Delete</a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('clients.index');

    }

    public function show($id)
    {
        $client = Client::where('id', $id)->first();

        return view('clients.show', ['Client' => $id], compact('client'));
    }

    public function show_invoices(Request $request, Client $client)
    {   
        $client_id = $client->id;
        if ($request->ajax()) {
            if ($request->name == 'list') {
                $data = Invoice::where('client_id', $client_id)->get();
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('online_invoice_url', function($row){

                        if($row->invoice_url) 
                            $url=$row->invoice_url;
                        else {
                            $url = '<a href="" data-toggle="tooltip" class="btn btn-secondary btn-xs">Generate</a>';
                        }
                        return $url;
                    })
                    ->addColumn('emailed', function($row){

                        if($row->is_emailed) 
                            $url="Yes";
                        else {
                            $url = '<a href="" data-toggle="tooltip"><i class="fas fa-envelope"></i></a> No ';
                        }
                        return $url;
                    })
                    ->addColumn('action', function($row){
                        $btn = '<a href="" data-toggle="tooltip" class="mr-1 btn btn-warning btn-sm">Authorize</a>';
                        $btn .= '<a href="" data-toggle="tooltip" class="mr-1 btn btn-primary btn-sm">Update</a>';
                        $btn .= '<a href="" data-toggle="tooltip" class="btn btn-danger btn-sm">Void</a>';

                        return $btn;
                    })
                    ->rawColumns(['action', 'online_invoice_url','emailed'])
                    ->make(true);
            } else if($request->name == 'jobs-list'){
                $data = DB::table('jobs as j')
                    ->leftJoin('invoices as i', 'i.job_id', '=', 'j.id')
                    ->leftJoin('clients as c', 'c.id', '=', 'j.client_id')
                    ->selectRaw('j.id, j.client_id, j.status, j.address, j.start_date_time, j.end_date_time, j.no_of_persons, i.invoice_id, i.invoice_url, c.company_name')
                    ->where('j.client_id', $client_id)
                    ->get();
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('invoice', function($row){
                        if($row->invoice_id) {
                            $inv = "<a href='".$row->invoice_url."'> ".$row->invoice_id."</a>";
                        } else {
                            $inv = '<a href="javascript:void(0)" data-compid="'.$row->client_id.'"  data-company="'.$row->company_name.'"  data-id="'.$row->id.'" data-toggle="tooltip" class="btn btn-secondary btn-xs generateBtn"><i class="fa-solid fa-gear"></i>Generate</a>';

                        }
                        return $inv;
                    })
                    ->addColumn('client', function($row){
                        $client = Client::where('id', $row->client_id)->first();
                        return $client->company_name;
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
                            $display .= '<a href="javascript:void(0)"  data-id="'.$row->id.'" data-toggle="tooltip" class="btn-xs assignBtn"><i class="fas fa-pen"></i></a>';


                        
                        return $display;
                    })
                    ->addColumn('action', function($row){
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editJob">Edit</a>';
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteJob">Delete</a>';

                        return $btn;
                    })
                    ->rawColumns(['action', 'assigned', 'invoice'])
                    ->make(true);
            }else {
                $data = Jobs::where('client_id', $client_id)->get();
                return Datatables::of($data)
                    ->addIndexColumn()
                    
                    ->addColumn('action', function($row){
                        $btn = '<a href="" data-toggle="tooltip" class="mr-1 btn btn-primary btn-sm">Edit</a>';
                        $btn .= '<a href="" data-toggle="tooltip" class="btn btn-danger btn-sm">Delete</a>';

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            
        }

        return view('clients.show', compact('client_id'));
    }

    public function create()
    {
        return view('clients.create', ['client' => new Client()]);
    }

    public function store(Request $request)
    {
        Client::updateOrCreate([
            'id' => $request->client_id
        ],
        [
            'client_name' => $request->client_name,
            'Name' => $request->company_name,
            'POBOX_AddressLine1' => $request->POBOX_AddressLine1,
            'POBOX_City' => $request->POBOX_City,
            'POBOX_Region' => $request->POBOX_Region,
            'POBOX_PostalCode' => $request->POBOX_PostalCode,
            'POBOX_Country' => $request->POBOX_Country,
            'PhoneNumber' => $request->PhoneNumber,
            'PhoneAreaCode' => $request->PhoneAreaCode,
            'rate_per_hour' => $request->rate_per_hour,
            'ot_rate_per_hour' => $request->ot_rate_per_hour,
            'company_name' => $request->company_name,
            'travel_allowance' => $request->travel_allowance,
            'holiday_rate' => $request->holiday_rate
        ]);

        return response()->json(['success'=>'Client saved successfully.']);
    }

    public function edit($id)
    {
        $client = Client::find($id);
        return response()->json($client);
    }

    public function destroy($id)
    {
        Client::find($id)->delete();

        return response()->json(['success'=>'Client deleted successfully.']);
    }
}
