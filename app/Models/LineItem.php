<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'invoice_id',
        'LineItemID',
        'Description',
        'UnitAmount',
        'TaxType',
        'TaxAmount',
        'LineAmount',
        'Quantity'
    ];
}
