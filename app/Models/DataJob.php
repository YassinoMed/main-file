<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataJob extends Model
{
    protected $fillable = [
        'type',
        'format',
        'status',
        'source',
        'mapping',
        'validation',
        'stats',
        'error',
        'created_by',
    ];

    protected $casts = [
        'mapping' => 'array',
        'validation' => 'array',
        'stats' => 'array',
    ];
}
