<?php

namespace App\Http\Controllers;

use App\Events\PusherEvent;
use Illuminate\Http\Request;

class PusherController extends Controller
{
    public function index()
    {
        return view('pusher.index');
    }

    public function fetch(Request $request)
    {
        return view('pusher.fetch', [
            'message' => $request->get('message'),
            'name' => 'Anony Mouse'
        ]);

    }

    public function broadcast(Request $request)
    {
        broadcast(new PusherEvent($request->get('message')))->toOthers();

        return view('pusher.broadcast', [
            'message' => $request->get('message'),
            'name' => 'Anony Mouse'
        ]);
    }
}
