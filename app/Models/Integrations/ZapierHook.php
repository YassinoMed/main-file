<?php

namespace App\Models\Integrations;

use Illuminate\Database\Eloquent\Model;

class ZapierHook extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'hook_url',
        'event',
        'filter',
        'is_active',
        'last_triggered_at',
    ];

    protected $casts = [
        'filter' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public static function getEvents()
    {
        return [
            'invoice.created' => 'Invoice Created',
            'invoice.paid' => 'Invoice Paid',
            'customer.created' => 'New Customer',
            'lead.created' => 'New Lead',
            'lead.converted' => 'Lead Converted',
            'proposal.created' => 'Proposal Created',
            'proposal.signed' => 'Proposal Signed',
            'employee.hired' => 'New Employee',
            'project.completed' => 'Project Completed',
        ];
    }

    public function trigger($data)
    {
        if (! $this->is_active) {
            return;
        }

        try {
            $client = new \GuzzleHttp\Client;
            $client->post($this->hook_url, [
                'json' => [
                    'event' => $this->event,
                    'data' => $data,
                    'timestamp' => now()->toIso8601String(),
                ],
                'timeout' => 30,
            ]);

            $this->update(['last_triggered_at' => now()]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
