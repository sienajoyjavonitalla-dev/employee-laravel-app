<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'client_name',
        'address',
        'rate_per_hour',
        'other_rate_per_hour',
        'ot_rate_per_hour',
        'abn',
        'company_name',
        'travel_allowance',
        'holiday_rate',
        'ContactID',
        'ContactStatus', 
        'Name',
        'POBOX_AddressLine1',
        'POBOX_City',
        'POBOX_Region',
        'POBOX_PostalCode',
        'POBOX_Country',
        'STREET_AddressLine1',
        'STREET_City',
        'STREET_Region',
        'STREET_PostalCode',
        'STREET_Country',
        'PhoneNumber',
        'PhoneAreaCode',
        'PhoneCountryCode'
    ];
}
