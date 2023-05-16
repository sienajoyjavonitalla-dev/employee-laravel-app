<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
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
                    ->addColumn('action', function($row){
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editClient">Edit</a>';
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteClient">Delete</a>';

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('clients.index');

    }

    public function show(Client $id)
    {
        return view('clients.index', ['Client' => $id]);
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
            'address' => $request->address,
            'rate_per_hour' => $request->rate_per_hour,
            'ot_rate_per_hour' => $request->ot_rate_per_hour,
            'abn' => $request->abn
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
