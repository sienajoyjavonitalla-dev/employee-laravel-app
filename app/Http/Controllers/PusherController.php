<?php

namespace App\Http\Controllers;

use App\Events\PusherEvent;
use Illuminate\Http\Request;
use App\Models\Messages;

class PusherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('pusher.index');
    }

    public function receive(Request $request)
    {
        return view('pusher.receive', [
            'message' => $request->get('message'),
            // 'name' => 'Anony Mouse 1'
        ]);

    }

    public function broadcast(Request $request)
    {
        broadcast(new PusherEvent($request->get('message')))->toOthers();

        return view('pusher.broadcast', [
            'message' => $request->get('message'),
            'name' => 'Anony Mouse 2'
        ]);
    }
}
