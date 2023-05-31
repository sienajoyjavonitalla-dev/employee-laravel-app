<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'client_name',
        'address',
        'rate_per_hour',
        'ot_rate_per_hour',
        'abn',
        'company_name',
        'travel_allowance'
    ];
}
