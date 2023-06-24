<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobAssignee;
use App\Models\Client;
use App\Models\User;
use App\Models\Jobs;
use App\Constants\StatusColorCodes;
use App\Models\LineItem;
use App\Models\Invoice;

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

            $events[] = [
                'title' => $appointment->id,
                'extendedProps' => [
                    'job_id' => $appointment->id,
                    'employee' => $assignee,
                    'client' => $clientName,
                    'status' => $status,
                    'invoiceUrl' => $invoiceUrl
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
