<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\User;
use App\Models\Jobs;
use App\Constants\StatusColorCodes;

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

            $clientName = Client::find($appointment->client_id)->client_name;
            $employeeName = User::find($appointment->user_id)->name;

            $status = Jobs::find($appointment->job_id)->status;

            $events[] = [
                'title' => "#".$appointment->job_id." ".$appointment->title,
                'extendedProps' => [
                    'comment' => 'This is a comment.',
                    'employee' => $employeeName,
                    'client' => $clientName,
                    'status' => $status
                ],
                'backgroundColor' => StatusColorCodes::$statusColorCodes[$status],
                'textColor' => '#000',
                'start' => $appointment->start_time,
                'end' => $appointment->finish_time,
                'allDay' => 'true',
                'display' => 'block'
            ];
        }
 
        return view('calendar', compact('events'));
    }

}
