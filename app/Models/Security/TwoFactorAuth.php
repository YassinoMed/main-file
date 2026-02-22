<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Model;

class TwoFactorAuth extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'secret',
        'enabled_at',
        'backup_codes',
    ];

    protected $casts = [
        'enabled_at' => 'datetime',
        'backup_codes' => 'array',
    ];

    protected $hidden = ['secret', 'backup_codes'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public static function getProviders()
    {
        return [
            'totp' => 'Authenticator App (TOTP)',
            'email' => 'Email Code',
            'sms' => 'SMS Code',
        ];
    }

    public function verifyCode($code)
    {
        if ($this->provider === 'totp') {
            return $this->verifyTOTP($code);
        }

        if ($this->provider === 'email') {
            return $this->verifyEmailCode($code);
        }

        return false;
    }

    protected function verifyTOTP($code)
    {
        $google2fa = new \PragmaRX\Google2FA\Google2FA;

        return $google2fa->verifyKey($this->secret, $code);
    }

    protected function verifyEmailCode($code)
    {
        return hash_equals($this->secret, $code);
    }

    public function generateBackupCodes()
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }
        $this->backup_codes = $codes;
        $this->save();

        return $codes;
    }

    public function useBackupCode($code)
    {
        $codes = $this->backup_codes ?? [];
        $index = array_search(strtoupper($code), $codes);

        if ($index !== false) {
            unset($codes[$index]);
            $this->backup_codes = array_values($codes);
            $this->save();

            return true;
        }

        return false;
    }
}
