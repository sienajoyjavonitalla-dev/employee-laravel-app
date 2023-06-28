<?php

namespace App\Http\Controllers;

use App\Events\PusherEvent;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PusherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        $messages = DB::table('messages')
                    ->leftJoin('users', 'messages.user_id', '=', 'users.id')
                    ->selectRaw('users.id, users.name, messages.message')
                    ->latest('messages.created_at')->get();

        return view('pusher.index',[
            'user_id' => $user->id
        ], compact('messages'));
    }

    public function receive(Request $request)
    {
        Log::info("receive: ".$request->get('name')." ".$request->get('message'));

        return view('pusher.receive', [
            'message' => $request->get('message'),
            'name' => $request->get('name')
        ]);

    }

    public function broadcast(Request $request)
    {
        $message = DB::transaction(function() use ($request) {

            $msgToSend = $request->get('message');
            $user = User::find($request->get('user_id'));            

            $message = Messages::create([
                'message' => $msgToSend,
                'user_id' => $user->id
            ]);

            Log::info("broadcast: ".$user->id." ".$user->name." ".$msgToSend);

            broadcast(new PusherEvent($msgToSend, $user->name, $user->id))->toOthers();

            return $message;
        });
        
        Log::info('->>> ' . $message->message);

        return view('pusher.broadcast', [
            'message' => $message->message,
            'name' => User::find($message->user_id)->name
        ]);
    }
}
