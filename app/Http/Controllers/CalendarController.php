<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobAssignee;
use App\Models\Client;
use App\Models\CalendarNote;
use App\Models\User;
use App\Models\Jobs;
use App\Constants\StatusColorCodes;
use App\Models\LineItem;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $events = [];

        if($user->roles == 'admin')
        {
            $appointments = Jobs::all();
        }
        else
        {
            $data = DB::table('jobs as j')
                    ->rightJoin('job_assignee as ja', 'j.id', '=', 'ja.job_id')
                    ->selectRaw('j.id, j.client_id, j.po_number, j.status, j.description, j.start_date_time, j.end_date_time, ja.assigned_id, ja.job_title')
                    ->where('ja.assigned_id', $user->id)
                    ->get();

            $appointments = $data;
        }       
 
        foreach ($appointments as $appointment) {

            $clientName = Client::find($appointment->client_id)->company_name;
            $jobAssignees = JobAssignee::where('job_id', $appointment->id)->get()->toArray();
            $lineItem = LineItem::where('job_id', $appointment->id)->first();

            $invoiceUrl = "";

            if($lineItem)
            {
                $invoice = Invoice::where('invoice_id', $lineItem->invoice_id)->first(); 
                $invoiceUrl = $invoice->invoice_url;
            }

            $status = $appointment->status;          
            $assignee = [];

            foreach($jobAssignees as $jobAssignee)
            {
                $assignee[] = [
                    "name" => User::find($jobAssignee['assigned_id'])->name,
                    "jobTitle" => $jobAssignee['job_title']
                ];
            }

            $jobTimeFrame = new Carbon($appointment->end_date_time);
            $newEndDateTime = $jobTimeFrame->addDays(1)->toDateString();

            $events[] = [
                'id' => $appointment->id,
                'title' => $appointment->id,
                'extendedProps' => [
                    'job_id' => $appointment->id,
                    'employee' => $assignee,
                    'client' => $clientName,
                    'description' => $appointment->description,
                    'poNumber' => $appointment->po_number,
                    'status' => $status,
                    'invoiceUrl' => $invoiceUrl,
                    'eventType' => 'job'
                ],
                'backgroundColor' => StatusColorCodes::$statusColorCodes[$status],
                'textColor' => '#000',
                'start' => $appointment->start_date_time,
                'end' => $newEndDateTime,
            ];
        }

        $calendarNotes = CalendarNote::all();

        foreach($calendarNotes as $calendarNote)
        {
            $events[] = [
                'id' => $calendarNote->id,
                'title' => $calendarNote->id,
                'extendedProps' => [
                    'message' => $calendarNote->message,
                    'eventType' => 'note'
                ],
                'backgroundColor' => '#e6db6c',
                'textColor' => '#000',
                'start' => $calendarNote->start_date,
            ];
        }
 
        return view('calendar', compact('events'));
    }

}
