<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use App\Models\XeroToken;
use App\Models\Invoice;

class GetInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $a = XeroToken::latest()->first();
        // dd($a);
        $client = new Client();
        $response= $client->request('GET', 'https://api.xero.com/api.xro/2.0/Invoices', [
            'headers' => [
                'Authorization' => 'Bearer '.$a->access_token,
                'Content-Type' => 'application/json',
                'xero-tenant-id' => env('XERO_TENANT_ID')
            ]
        ]);

        $results = json_decode($response->getBody()->getContents());
        $stats = [];
        if($response->getStatusCode() == 200) {
            foreach($results->Invoices as $i) {
                Invoice::updateOrCreate(['invoice_id' => $i->InvoiceID],
                [
                    'type' => $i->Type,
                    'invoice_number' => $i->InvoiceNumber,
                    'amount_due' => $i->AmountDue,
                    'amount_paid' => $i->AmountPaid,
                    'amount_credited' => $i->AmountCredited,
                    'has_attachments' => $i->HasAttachments,
                    'date_string' => $i->DateString,
                    'duedate_string' => $i->DueDateString,
                    'status' => $i->Status,
                    'subtotal' => $i->SubTotal,
                    'TotalTax' => $i->TotalTax,
                    'Total' => $i->Total,
                    'currency_code' => $i->CurrencyCode,
                    'updated_date_utc' => $i->UpdatedDateUTC,
                    'fully_paid_date_utc' => $i->FullyPaidOnDate ?? ''
                ]);
            }
        }

        return 0;
    }
}
