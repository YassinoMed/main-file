<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsDashboard extends Model
{
    protected $fillable = [
        'name',
        'description',
        'filters',
        'created_by',
    ];

    protected $casts = [
        'filters' => 'array',
    ];

    public function widgets()
    {
        return $this->hasMany(AnalyticsWidget::class, 'dashboard_id', 'id');
    }
}
