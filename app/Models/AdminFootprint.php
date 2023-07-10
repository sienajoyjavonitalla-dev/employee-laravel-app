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
        'entity_id',
        'entity_target',
        'entity_target_id',
        'field',
        'description',
        'prev_value',
        'new_value'
    ];
}
