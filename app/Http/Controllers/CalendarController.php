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
                'title' => "Job #".$appointment->job_id." ".$appointment->title,
                'extendedProps' => [
                    'comment' => 'This is a comment.'
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
