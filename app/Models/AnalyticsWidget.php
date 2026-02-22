<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsWidget extends Model
{
    protected $fillable = [
        'dashboard_id',
        'type',
        'config',
        'position',
        'created_by',
    ];

    protected $casts = [
        'config' => 'array',
        'position' => 'array',
    ];

    public function dashboard()
    {
        return $this->belongsTo(AnalyticsDashboard::class, 'dashboard_id', 'id');
    }
}
