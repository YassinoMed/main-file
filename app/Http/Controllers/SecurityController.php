<?php

namespace App\Http\Controllers;

use App\Models\Security\IpRestriction;
use App\Models\Security\TwoFactorAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecurityController extends Controller
{
    public function index()
    {
        $twoFactor = TwoFactorAuth::where('user_id', Auth::id())->first();
        $ipRestrictions = IpRestriction::where('user_id', Auth::id())->get();

        return view('security.index', compact('twoFactor', 'ipRestrictions'));
    }

    public function enableTwoFactor(Request $request)
    {
        $provider = $request->provider ?? 'totp';

        $twoFactor = TwoFactorAuth::updateOrCreate(
            ['user_id' => Auth::id()],
            ['provider' => $provider]
        );

        if ($provider === 'totp') {
            $google2fa = new \PragmaRX\Google2FA\Google2FA;
            $secret = $google2fa->generateSecretKey();

            $twoFactor->secret = $secret;
            $twoFactor->save();

            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                Auth::user()->email,
                $secret
            );

            return response()->json([
                'secret' => $secret,
                'qr_code_url' => $qrCodeUrl,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $twoFactor = TwoFactorAuth::where('user_id', Auth::id())->first();

        if (! $twoFactor) {
            return response()->json(['error' => '2FA not configured'], 422);
        }

        if ($twoFactor->provider === 'totp') {
            $google2fa = new \PragmaRX\Google2FA\Google2FA;
            $valid = $google2fa->verifyKey($twoFactor->secret, $request->code);
        } else {
            $valid = $twoFactor->secret === $request->code;
        }

        if ($valid) {
            $twoFactor->enabled_at = now();
            $twoFactor->backup_codes = $twoFactor->generateBackupCodes();
            $twoFactor->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Invalid code'], 422);
    }

    public function disableTwoFactor()
    {
        TwoFactorAuth::where('user_id', Auth::id())->delete();

        return redirect()->back()->with('success', 'Two-factor authentication disabled');
    }

    public function getBackupCodes()
    {
        $twoFactor = TwoFactorAuth::where('user_id', Auth::id())->first();

        if (! $twoFactor || ! $twoFactor->backup_codes) {
            return response()->json(['error' => 'No backup codes'], 422);
        }

        return response()->json(['backup_codes' => $twoFactor->backup_codes]);
    }

    public function verifyWithBackupCode(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $twoFactor = TwoFactorAuth::where('user_id', Auth::id())->first();

        if (! $twoFactor) {
            return response()->json(['error' => '2FA not configured'], 422);
        }

        if ($twoFactor->useBackupCode($request->code)) {
            return response()->json(['success' => true, 'remaining' => count($twoFactor->backup_codes)]);
        }

        return response()->json(['error' => 'Invalid backup code'], 422);
    }

    public function addIpRestriction(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'description' => 'nullable|string',
            'is_whitelist' => 'boolean',
            'expires_at' => 'nullable|date',
        ]);

        IpRestriction::create([
            'user_id' => Auth::id(),
            'ip_address' => $request->ip_address,
            'description' => $request->description,
            'is_whitelist' => $request->is_whitelist ?? true,
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'IP restriction added');
    }

    public function deleteIpRestriction(IpRestriction $ipRestriction)
    {
        $this->authorize('delete', $ipRestriction);
        $ipRestriction->delete();

        return redirect()->back()->with('success', 'IP restriction deleted');
    }

    public function toggleIpRestriction(IpRestriction $ipRestriction)
    {
        $this->authorize('update', $ipRestriction);
        $ipRestriction->is_active = ! $ipRestriction->is_active;
        $ipRestriction->save();

        return redirect()->back();
    }
}
