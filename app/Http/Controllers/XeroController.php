<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use App\Models\XeroToken;

class XeroController extends Controller
{
    public function get_started() {
        $url = 'https://login.xero.com/identity/connect/authorize?response_type=code&client_id=2BC3D6774F2A482D81379F568045B2FD&redirect_uri='.urlencode(env('XERO_REDIRECT_URL')).'&scope=openid profile email accounting.transactions&state=123';
        
        return Redirect::to($url);
    }

    public function callback(Request $req) {
        // dd($req->code);
        $body = [
            'grant_type' => 'authorization_code',
            'code' => $req->code,
            'redirect_uri' => env('XERO_REDIRECT_URL')
        ];

        $client = new Client();
        $response= $client->request('POST', 'https://identity.xero.com/connect/token', [
            'headers' => [
                'Authorization' => 'Basic '.base64_encode(env('XERO_CLIENT_ID').':'.env('XERO_CLIENT_SECRET')),
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json'

            ],
            'form_params' => $body
        ]);

        $results = json_decode($response->getBody()->getContents());
        // dd($response->getStatusCode());
        $stats = [];
        if($response->getStatusCode() == 200) {
            $token = XeroToken::updateOrCreate(['refresh_token' => $results->refresh_token],
            [
                'id_token' => $results->id_token,
                'access_token' => $results->access_token,
                'expires_in' => $results->expires_in,
                'token_type' => $results->token_type,
                'scopes' => $results->scope
            ]);

            $stats = ['success' => 'Success'];
        } else {
            $stats = ['error' => 'error'];

        }

        return response($stats, 200)->header('Content-Type', 'application/json');
    }

    public function connections() {
        $a = XeroToken::latest()->first();
        $client = new Client();
        $response= $client->request('GET', 'https://api.xero.com/connections', [
            'headers' => [
                'Authorization' => 'Bearer '.$a->access_token,
                'Content-Type' => 'application/json'

            ]
        ]);

        $results = json_decode($response->getBody()->getContents());
        dd($results);
        $stats = [];
        if($response->getStatusCode() == 200) {
        }
    }

    public function generate_invoice() {
        return 0;
    }

}
