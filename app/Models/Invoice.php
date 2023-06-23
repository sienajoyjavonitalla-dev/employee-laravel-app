<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'client_id',
        'type',
        'invoice_id',
        'invoice_number',
        'amount_due',
        'amount_paid',
        'amount_credited',
        'has_attachments',
        'date_string',
        'duedate_string',
        'branding_theme_id',
        'status',
        'subtotal',
        'TotalTax',
        'Total',
        'currency_code',
        'updated_date_utc',
        'fully_paid_date_utc',
        'updated_at',
        'created_at',
    ];
}
