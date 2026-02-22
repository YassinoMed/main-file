<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Model;

class IpRestriction extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'description',
        'is_whitelist',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_whitelist' => 'boolean',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function isAllowed()
    {
        if (! $this->is_active) {
            return true;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        $currentIp = request()->ip();

        if ($this->is_whitelist) {
            return $this->ip_address === $currentIp || $this->ip_address === '*';
        }

        return $this->ip_address !== $currentIp;
    }

    public static function isIpAllowed($userId)
    {
        $restrictions = self::where('user_id', $userId)->where('is_active', true)->get();

        if ($restrictions->isEmpty()) {
            return true;
        }

        foreach ($restrictions as $restriction) {
            if ($restriction->isAllowed()) {
                return $restriction->is_whitelist;
            }
        }

        return false;
    }
}
