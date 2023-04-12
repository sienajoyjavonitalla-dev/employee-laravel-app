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
        'description',
        'start_date_time',
        'end_date_time',
        'job_address',
        'updated_at',
        'deleted_at',
    ];
}
