<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupSnapshot extends Model
{
    protected $fillable = [
        'provider',
        'location',
        'status',
        'metadata',
        'started_at',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
