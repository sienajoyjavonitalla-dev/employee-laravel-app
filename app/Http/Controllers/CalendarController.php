<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $events = [];
 
        $appointments = Appointment::all();
 
        foreach ($appointments as $appointment) {
            $events[] = [
                'title' => $appointment->client_id . ' ('.$appointment->user_id.')',
                'start' => $appointment->start_time,
                'end' => $appointment->finish_time,
            ];
        }
 
        return view('calendar', compact('events'));
    }

}
