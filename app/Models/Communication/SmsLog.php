<?php

namespace App\Models\Communication;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'user_id',
        'to',
        'from',
        'message',
        'status',
        'provider',
        'external_id',
        'sent_at',
        'delivered_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public static function getStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'queued' => 'Queued',
            'sent' => 'Sent',
            'delivered' => 'Delivered',
            'failed' => 'Failed',
        ];
    }

    public static function getProviders()
    {
        return [
            'twilio' => 'Twilio',
            'msg91' => 'MSG91',
            'nexmo' => 'Nexmo (Vonage)',
            'aws_sns' => 'AWS SNS',
            'custom' => 'Custom Provider',
        ];
    }
}
