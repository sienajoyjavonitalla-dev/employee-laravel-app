<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\XeroToken;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use Illuminate\Support\Facades\Log;

class RefreshToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xero:refresh_token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh token every 30minutes';

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
        Log::info("XERO REFRESH COMMAND SCHEDULING WORKS");

        // $a = XeroToken::latest()->first();

        // $body = [
        //     'grant_type' => 'refresh_token',
        //     'refresh_token' => $a->refresh_token
        // ];

        // $client = new Client();
        // $response= $client->request('POST', 'https://identity.xero.com/connect/token', [
        //     'headers' => [
        //         'Authorization' => 'Basic '.base64_encode(env('XERO_CLIENT_ID').':'.env('XERO_CLIENT_SECRET')),
        //         'Content-Type' => 'application/x-www-form-urlencoded',
        //         'Accept' => 'application/json'

        //     ],
        //     'form_params' => $body
        // ]);

        // $results = json_decode($response->getBody()->getContents());
        // $stats = [];
        // if($response->getStatusCode() == 200) {
        //     $token = XeroToken::updateOrCreate(['refresh_token' => $results->refresh_token],
        //     [
        //         'id_token' => $results->id_token,
        //         'access_token' => $results->access_token,
        //         'expires_in' => $results->expires_in,
        //         'token_type' => $results->token_type,
        //         'scopes' => $results->scope
        //     ]);

        //     $stats = ['success' => 'Success'];
        // } else {
        //     $stats = ['error' => 'error'];

        // }

        return 0;
    }
}
