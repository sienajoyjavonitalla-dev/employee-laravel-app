<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobs extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'client_id',
        'employee_id',
        'status',
        'po_number',
        'description',
        'start_date_time',
        'end_date_time',
        'address',
        'updated_at',
        'deleted_at',
        'start_time',
        'end_time',
        'timesheet',
        'type'
    ];
}
