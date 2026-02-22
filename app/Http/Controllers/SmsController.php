<?php

namespace App\Http\Controllers;

use App\Models\Communication\SmsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsController extends Controller
{
    public function index()
    {
        $logs = SmsLog::where('user_id', Auth::user()->creatorId())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('sms.index', compact('logs'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'message' => 'required|string|max:1600',
            'provider' => 'nullable|string',
        ]);

        $provider = $request->provider ?? 'twilio';
        
        $result = $this->sendSms($request->to, $request->message, $provider);
        
        $log = SmsLog::create([
            'user_id' => Auth::user()->creatorId(),
            'to' => $request->to,
            'message' => $request->message,
            'provider' => $provider,
            'status' => $result['status'],
            'external_id' => $result['external_id'] ?? null,
            'sent_at' => now(),
        ]);

        if ($result['status'] === 'sent' || $result['status'] === 'queued') {
            return response()->json(['success' => true, 'log_id' => $log->id]);
        }
        
        return response()->json(['error' => $result['error'] ?? 'Failed to send SMS'], 422);
    }

    protected function sendSms($to, $message, $provider)
    {
        switch ($provider) {
            case 'twilio':
                return $this->sendTwilio($to, $message);
            case 'msg91':
                return $this->sendMsg91($to, $message);
            case 'nexmo':
                return $this->sendNexmo($to, $message);
            default:
                return ['status' => 'failed', 'error' => 'Unknown provider'];
        }
    }

    protected function sendTwilio($to, $message)
    {
        try {
            $twilio = new \Twilio\Rest\Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
            
            $result = $twilio->messages->create($to, [
                'from' => config('services.twilio.from'),
                'body' => $message,
            ]);
            
            return [
                'status' => 'sent',
                'external_id' => $result->sid,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function sendMsg91($to, $message)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://api.msg91.com/api/v5/send', [
                'form_params' => [
                    'authkey' => config('services.msg91.authkey'),
                    'mobile' => $to,
                    'message' => $message,
                    'sender' => config('services.msg91.sender'),
                    'route' => '4',
                ],
            ]);
            
            return ['status' => 'sent'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    protected function sendNexmo($to, $message)
    {
        try {
            $client = new \Nexmo\Client(new \Nexmo\Client\Credentials\Basic(
                config('services.nexmo.key'),
                config('services.nexmo.secret')
            ));
            
            $result = $client->message()->send([
                'to' => $to,
                'from' => config('services.nexmo.from'),
                'text' => $message,
            ]);
            
            return ['status' => 'sent', 'external_id' => $result['message-id']];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    public function test(Request $request)
    {
        $request->validate([
            'provider' => 'required|string',
        ]);

        $testNumber = config('settings.test_sms_number');
        
        if (!$testNumber) {
            return response()->json(['error' => 'Test number not configured'], 422);
        }

        $result = $this->sendSms($testNumber, 'Test message from ERPGo', $request->provider);
        
        return response()->json($result);
    }

    public function webhook(Request $request)
    {
        $provider = $request->provider ?? 'twilio';
        
        switch ($provider) {
            case 'twilio':
                $messageSid = $request->MessageSid;
                $status = $request->MessageStatus;
                break;
            case 'nexmo':
                $messageSid = $request->['message-id'];
                $status = $request->status;
                break;
        }

        if (isset($messageSid)) {
            $log = SmsLog::where('external_id', $messageSid)->first();
            
            if ($log) {
                $log->status = $status;
                
                if ($status === 'delivered') {
                    $log->delivered_at = now();
                }
                
                $log->save();
            }
        }

        return response()->json(['success' => true]);
    }
}
