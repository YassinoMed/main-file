<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class ReportSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'report_type',
        'frequency',
        'recipients',
        'filters',
        'last_sent_at',
        'is_active',
    ];

    protected $casts = [
        'recipients' => 'array',
        'filters' => 'array',
        'last_sent_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public static function getFrequencyOptions()
    {
        return [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
        ];
    }

    public static function getReportTypes()
    {
        return [
            'sales' => 'Sales Report',
            'revenue' => 'Revenue Report',
            'expenses' => 'Expenses Report',
            'employees' => 'Employees Report',
            'projects' => 'Projects Report',
            'invoices' => 'Invoices Report',
            'leads' => 'Leads Report',
            'inventory' => 'Inventory Report',
        ];
    }

    public function shouldSend()
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();
        $lastSent = $this->last_sent_at;

        switch ($this->frequency) {
            case 'daily':
                return ! $lastSent || $lastSent->diffInDays($now) >= 1;
            case 'weekly':
                return ! $lastSent || $lastSent->diffInWeeks($now) >= 1;
            case 'monthly':
                return ! $lastSent || $lastSent->diffInMonths($now) >= 1;
            case 'quarterly':
                return ! $lastSent || $lastSent->diffInMonths($now) >= 3;
            default:
                return false;
        }
    }
}
