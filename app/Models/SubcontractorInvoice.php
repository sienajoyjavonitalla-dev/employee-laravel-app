<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubcontractorInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'bank_id', 
        'subcontractor_id',
        'from_date',
        'to_date'
    ];
}
