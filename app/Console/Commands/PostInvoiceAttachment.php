<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Models\Jobs;
use App\Models\Appointment;
use App\Models\XeroToken;
use DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\User;
use App\Models\JobAssignee;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;

class PostInvoiceAttachment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:invoice-attach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'iattach ang post kasabot rka ana';

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

        // Path to the RAR file

        $rarFilePath = public_path('img\uprisee.rar');
        $filename = 'uprisee.rar';
        $invoice_id = "2d916afe-5362-4cda-99bd-1ef05f7c5fec";

        // Read the contents of the RAR file
        $fileContents = file_get_contents($rarFilePath);

        $body = [
            $fileContents
        ];

        // Create a Guzzle HTTP client
        $client = new Client();

        // Create a Guzzle HTTP request with the RAR file in the request body
        $response = $client->request('POST', 'https://api.xero.com/api.xro/2.0/Invoices/'.$invoice_id.'/Attachments/'.$filename,  [
            'headers' => [
                'Authorization' => 'Bearer '.$a->access_token,
                'Content-Type' => 'application/octet-stream',
                'xero-tenant-id' => env('XERO_TENANT_ID'),
                'Accept' => 'application/json'
            ],
            'form_params' => $body]);

        $results = json_decode($response->getBody()->getContents());
        dd($results);
        return 0;
    }
}
