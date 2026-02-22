<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class DashboardWidget extends Model
{
    protected $fillable = [
        'user_id',
        'widget_type',
        'title',
        'position',
        'settings',
        'is_visible',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_visible' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public static function getAvailableWidgets()
    {
        return [
            'sales_chart' => [
                'name' => 'Sales Chart',
                'description' => 'Display sales trends',
                'icon' => 'ti ti-chart-line',
            ],
            'revenue_card' => [
                'name' => 'Revenue Card',
                'description' => 'Show total revenue',
                'icon' => 'ti ti-currency-dollar',
            ],
            'invoices_status' => [
                'name' => 'Invoices Status',
                'description' => 'Invoices by status',
                'icon' => 'ti ti-file-invoice',
            ],
            'leads_pipeline' => [
                'name' => 'Leads Pipeline',
                'description' => 'Leads conversion funnel',
                'icon' => 'ti ti-funnel',
            ],
            'employees_stats' => [
                'name' => 'Employees Stats',
                'description' => 'Employee statistics',
                'icon' => 'ti ti-users',
            ],
            'projects_progress' => [
                'name' => 'Projects Progress',
                'description' => 'Active projects status',
                'icon' => 'ti ti-folder',
            ],
            'recent_activities' => [
                'name' => 'Recent Activities',
                'description' => 'Latest system activities',
                'icon' => 'ti ti-activity',
            ],
            'tasks_todo' => [
                'name' => 'Tasks To-Do',
                'description' => 'Pending tasks',
                'icon' => 'ti ti-checkbox',
            ],
            'calendar_widget' => [
                'name' => 'Calendar',
                'description' => 'Upcoming events',
                'icon' => 'ti ti-calendar',
            ],
            'goals_tracker' => [
                'name' => 'Goals Tracker',
                'description' => 'Track KPIs and goals',
                'icon' => 'ti ti-target',
            ],
        ];
    }
}
