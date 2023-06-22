<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAssignee extends Model
{
    use HasFactory;
    protected $table = 'job_assignee';

    protected $fillable = [
        'job_id',
        'assigned_id',
        'job_title',
        'created_at',
        'updated_at'
    ];
}
