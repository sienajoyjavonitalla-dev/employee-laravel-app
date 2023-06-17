<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XeroToken extends Model
{
    use HasFactory;
     protected $table = 'xero_tokens';

    protected $fillable = [
        'id_token',
        'access_token',
        'expires_in',
        'token_type',
        'refresh_token',
        'scopes'
    ];
}
