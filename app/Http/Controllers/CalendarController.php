<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobAssignee;
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
 
        $appointments = Jobs::all();
 
        foreach ($appointments as $appointment) {

            $clientName = Client::find($appointment->client_id)->client_name;
            $status = $appointment->status;

            $jobAssignees = JobAssignee::where('job_id', $appointment->id)->get()->toArray();

            $assignee = [];

            foreach($jobAssignees as $jobAssignee)
            {
                $assignee[] = [
                    "name" => User::find($jobAssignee['assigned_id'])->name,
                    "jobTitle" => $jobAssignee['job_title']
                ];
            }

            $events[] = [
                'title' => $appointment->id,
                'extendedProps' => [
                    'job_id' => $appointment->id,
                    'employee' => $assignee,
                    'client' => $clientName,
                    'status' => $status
                ],
                'backgroundColor' => StatusColorCodes::$statusColorCodes[$status],
                'textColor' => '#000',
                'start' => $appointment->start_date_time,
                'allDay' => 'true',
                'display' => 'block'
            ];
        }
 
        return view('calendar', compact('events'));
    }

}
