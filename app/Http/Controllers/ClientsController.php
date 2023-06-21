<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Jobs;
use DataTables;

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
            } else {
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
