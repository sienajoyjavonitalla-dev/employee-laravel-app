<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminFootprint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action_type',
        'entity',
        'field',
        'prev_value',
        'new_value'
    ];
}
