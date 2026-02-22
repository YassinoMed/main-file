<?php

namespace App\Models\Integrations;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'url',
        'method',
        'events',
        'headers',
        'secret',
        'is_active',
        'last_triggered_at',
        'failure_count',
    ];

    protected $casts = [
        'events' => 'array',
        'headers' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    protected $hidden = ['secret'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public static function getEvents()
    {
        return [
            'invoice.created' => 'Invoice Created',
            'invoice.paid' => 'Invoice Paid',
            'invoice.overdue' => 'Invoice Overdue',
            'customer.created' => 'Customer Created',
            'lead.created' => 'Lead Created',
            'lead.stage_changed' => 'Lead Stage Changed',
            'employee.created' => 'Employee Created',
            'project.created' => 'Project Created',
            'task.completed' => 'Task Completed',
            'leave.approved' => 'Leave Approved',
            'stock.low' => 'Stock Low',
            'ticket.created' => 'Ticket Created',
        ];
    }

    public static function getMethods()
    {
        return ['GET', 'POST', 'PUT', 'PATCH'];
    }

    public function trigger($event, $data)
    {
        if (! $this->is_active) {
            return;
        }
        if (! in_array($event, $this->events)) {
            return;
        }

        try {
            $client = new \GuzzleHttp\Client;
            $response = $client->request($this->method, $this->url, [
                'json' => $data,
                'headers' => array_merge($this->headers ?? [], [
                    'Content-Type' => 'application/json',
                    'X-Webhook-Event' => $event,
                    'X-Webhook-Signature' => $this->generateSignature($data),
                ]),
                'timeout' => 30,
            ]);

            $this->update([
                'last_triggered_at' => now(),
                'failure_count' => 0,
            ]);

            return true;
        } catch (\Exception $e) {
            $this->increment('failure_count');

            return false;
        }
    }

    protected function generateSignature($data)
    {
        return hash_hmac('sha256', json_encode($data), $this->secret);
    }
}
