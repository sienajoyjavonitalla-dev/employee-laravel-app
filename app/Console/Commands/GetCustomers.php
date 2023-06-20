<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use App\Models\XeroToken;
use App\Models\Customer;


class GetCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get customers from Xero';

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
        $response= $client->request('GET', 'https://api.xero.com/api.xro/2.0/contacts?where=IsCustomer=true', [
            'headers' => [
                'Authorization' => 'Bearer '.$a->access_token,
                'Content-Type' => 'application/json',
                'xero-tenant-id' => env('XERO_TENANT_ID')
            ]
        ]);

        $results = json_decode($response->getBody()->getContents());
        $stats = [];
        if($response->getStatusCode() == 200) {
            foreach($results->Contacts as $i) {

                foreach($i->Addresses as $add) {
                    if($add->AddressType == 'POBOX') {
                        $POBOX_AddressLine1 = $add->AddressLine1 ?? '';
                        $POBOX_City = $add->City ?? '';
                        $POBOX_Region = $add->Region ?? '';
                        $POBOX_PostalCode = $add->PostalCode ?? '';
                        $POBOX_Country = $add->Country ?? '';
                    }

                    if($add->AddressType == 'STREET') {
                        $STREET_AddressLine1 = $add->AddressLine1 ?? '';
                        $STREET_City = $add->City ?? '';
                        $STREET_Region = $add->Region ?? '';
                        $STREET_PostalCode = $add->PostalCode ?? '';
                        $STREET_Country = $add->Country ?? '';
                    }
                }

                foreach($i->Phones as $p) {
                    if($add->AddressType == 'DEFAULT') {
                        $PhoneNumber = $p->PhoneNumber ?? '';
                        $PhoneAreaCode = $p->PhoneAreaCode ?? '';
                        $PhoneCountryCode = $p->PhoneCountryCode ?? '';
                    }
                }
                Customer::updateOrCreate(['ContactID' => $i->ContactID, 'ContactStatus' => $i->ContactStatus],
                [
                    'ContactID' => $i->ContactID,
                    'ContactStatus' => $i->ContactStatus, 
                    'Name' => $i->Name,
                    'POBOX_AddressLine1' => $POBOX_AddressLine1,
                    'POBOX_City' => $POBOX_City,
                    'POBOX_Region' => $POBOX_Region,
                    'POBOX_PostalCode' => $POBOX_PostalCode,
                    'POBOX_Country' => $POBOX_Country,
                    'STREET_AddressLine1' => $STREET_AddressLine1,
                    'STREET_City' => $STREET_City,
                    'STREET_Region' => $STREET_Region,
                    'STREET_PostalCode' => $STREET_PostalCode,
                    'STREET_Country' => $STREET_Country,
                    'PhoneNumber' => $PhoneNumber ?? '',
                    'PhoneAreaCode' => $PhoneAreaCode ?? '',
                    'PhoneCountryCode' => $PhoneCountryCode ?? '',
                    'company_name' => $i->Name
                ]);
            }
        }

        return 0;
    }
}
