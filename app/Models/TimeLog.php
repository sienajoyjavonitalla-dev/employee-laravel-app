<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'assigned_id',
        'start_time',
        'end_time',
        'date',
        'timesheet',
        'lunch_break',
        'updated_at',
        'created_at',
    ];
}
