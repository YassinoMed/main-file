<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'route',
        'method',
        'ip_address',
        'user_agent',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];
}
