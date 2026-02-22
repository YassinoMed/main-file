<?php

namespace App\Models\Integrations;

use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'access_token',
        'refresh_token',
        'expires_at',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $encrypted = ['access_token', 'refresh_token'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public static function getAvailableProviders()
    {
        return [
            'google' => [
                'name' => 'Google',
                'description' => 'Google Calendar, Gmail, Drive',
                'icon' => 'ti ti-brand-google',
                'scopes' => [
                    'https://www.googleapis.com/auth/calendar',
                    'https://www.googleapis.com/auth/gmail.readonly',
                ],
            ],
            'microsoft' => [
                'name' => 'Microsoft 365',
                'description' => 'Outlook, OneDrive, Teams',
                'icon' => 'ti ti-brand-windows',
                'scopes' => [
                    'Calendars.ReadWrite',
                    'Mail.ReadWrite',
                ],
            ],
            'slack' => [
                'name' => 'Slack',
                'description' => 'Team communication',
                'icon' => 'ti ti-brand-slack',
                'scopes' => [
                    'channels:read',
                    'chat:write',
                ],
            ],
            'zoom' => [
                'name' => 'Zoom',
                'description' => 'Video meetings',
                'icon' => 'ti ti-video',
                'scopes' => [
                    'meeting:write',
                ],
            ],
            'twilio' => [
                'name' => 'Twilio',
                'description' => 'SMS notifications',
                'icon' => 'ti ti-message',
                'scopes' => [],
            ],
            'zapier' => [
                'name' => 'Zapier',
                'description' => 'Connect 5000+ apps',
                'icon' => 'ti ti-plug',
                'scopes' => [],
            ],
            'stripe' => [
                'name' => 'Stripe',
                'description' => 'Payment processing',
                'icon' => 'ti ti-credit-card',
                'scopes' => [],
            ],
        ];
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
