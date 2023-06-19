<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\User;

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

            $client = Client::find($appointment->client_id);
            $employee = User::find($appointment->user_id);

            $events[] = [
                'title' => "Job #".$appointment->job_id." ".$appointment->title,
                'extendedProps' => [
                    'comment' => 'This is a comment.',
                    'employee' => $employee->name,
                    'client' => $client->client_name
                ],
                'start' => $appointment->start_time,
                'end' => $appointment->finish_time,
                'allDay' => 'true',
                'display' => 'block'
            ];
        }
 
        return view('calendar', compact('events'));
    }

}
