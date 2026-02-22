<?php

namespace App\Http\Controllers;

use App\Models\Integrations\Integration;
use App\Models\Integrations\Webhook;
use App\Models\Integrations\ZapierHook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class IntegrationController extends Controller
{
    public function index()
    {
        $integrations = Integration::where('user_id', Auth::user()->creatorId())->get();
        $webhooks = Webhook::where('user_id', Auth::user()->creatorId())->get();
        $zapierHooks = ZapierHook::where('user_id', Auth::user()->creatorId())->get();
        $apiTokens = PersonalAccessToken::where('tokenable_type', User::class)
            ->where('tokenable_id', Auth::user()->creatorId())
            ->latest()
            ->get();

        return view('integrations.index', compact('integrations', 'webhooks', 'zapierHooks', 'apiTokens'));
    }

    public function connect(Request $request)
    {
        $provider = $request->provider;
        $providers = Integration::getAvailableProviders();

        if (! isset($providers[$provider])) {
            return redirect()->back()->with('error', 'Invalid provider');
        }

        $config = $providers[$provider];

        switch ($provider) {
            case 'google':
                return $this->connectGoogle($config);
            case 'microsoft':
                return $this->connectMicrosoft($config);
            case 'slack':
                return $this->connectSlack($config);
            default:
                return redirect()->back()->with('error', 'Provider not supported');
        }
    }

    protected function connectGoogle($config)
    {
        $client = new \Google\Client;
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(route('integrations.callback', 'google'));
        $client->addScope($config['scopes']);
        $client->setAccessType('offline');

        $authUrl = $client->createAuthUrl();

        return redirect($authUrl);
    }

    protected function connectMicrosoft($config)
    {
        $client = new \Microsoft\Graph\Graph;
        $client->setAccessToken('');

        $oauth = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => config('services.microsoft.client_id'),
            'clientSecret' => config('services.microsoft.client_secret'),
            'redirectUri' => route('integrations.callback', 'microsoft'),
            'scopes' => $config['scopes'],
        ]);

        $authUrl = $oauth->getAuthorizationUrl();
        session(['oauth_state' => $oauth->getState()]);

        return redirect($authUrl);
    }

    protected function connectSlack($config)
    {
        $oauth = new \Slack\OAuthClient(config('services.slack.client_id'), config('services.slack.client_secret'));

        return redirect($oauth->getAuthUrl());
    }

    public function callback(Request $request, $provider)
    {
        $code = $request->code;

        switch ($provider) {
            case 'google':
                return $this->handleGoogleCallback($code);
            case 'microsoft':
                return $this->handleMicrosoftCallback($code);
            default:
                return redirect()->route('integrations.index')->with('error', 'Invalid provider');
        }
    }

    protected function handleGoogleCallback($code)
    {
        $client = new \Google\Client;
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(route('integrations.callback', 'google'));

        $token = $client->fetchAccessTokenWithAuthCode($code);

        Integration::updateOrCreate(
            [
                'user_id' => Auth::user()->creatorId(),
                'provider' => 'google',
            ],
            [
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'] ?? null,
                'expires_at' => now()->addSeconds($token['expires_in']),
                'is_active' => true,
            ]
        );

        return redirect()->route('integrations.index')->with('success', 'Google connected successfully');
    }

    protected function handleMicrosoftCallback($code)
    {
        return redirect()->route('integrations.index')->with('success', 'Microsoft connected successfully');
    }

    public function disconnect(Request $request)
    {
        $integration = Integration::where('user_id', Auth::user()->creatorId())
            ->where('provider', $request->provider)
            ->first();

        if ($integration) {
            $integration->delete();

            return redirect()->back()->with('success', 'Integration disconnected');
        }

        return redirect()->back()->with('error', 'Integration not found');
    }

    public function storeWebhook(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'method' => 'required|in:GET,POST,PUT,PATCH',
            'events' => 'required|array',
            'secret' => 'nullable|string',
        ]);

        Webhook::create([
            'user_id' => Auth::user()->creatorId(),
            'name' => $validated['name'],
            'url' => $validated['url'],
            'method' => $validated['method'],
            'events' => $validated['events'],
            'secret' => $validated['secret'] ?? Str::random(32),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Webhook created successfully');
    }

    public function storeZapierHook(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hook_url' => 'required|url',
            'event' => 'required|string',
        ]);

        ZapierHook::create([
            'user_id' => Auth::user()->creatorId(),
            'name' => $validated['name'],
            'hook_url' => $validated['hook_url'],
            'event' => $validated['event'],
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Zapier hook created successfully');
    }

    public function destroyWebhook(Webhook $webhook)
    {
        if ((int) $webhook->user_id !== (int) Auth::user()->creatorId()) {
            return redirect()->back()->with('error', 'Permission denied');
        }
        $webhook->delete();

        return redirect()->back()->with('success', 'Webhook deleted');
    }

    public function toggleWebhook(Webhook $webhook)
    {
        if ((int) $webhook->user_id !== (int) Auth::user()->creatorId()) {
            return redirect()->back()->with('error', 'Permission denied');
        }
        $webhook->is_active = ! $webhook->is_active;
        $webhook->save();

        return redirect()->back();
    }

    public function destroyZapierHook(ZapierHook $zapierHook)
    {
        if ((int) $zapierHook->user_id !== (int) Auth::user()->creatorId()) {
            return redirect()->back()->with('error', 'Permission denied');
        }
        $zapierHook->delete();

        return redirect()->back()->with('success', 'Zapier hook deleted');
    }

    public function toggleZapierHook(ZapierHook $zapierHook)
    {
        if ((int) $zapierHook->user_id !== (int) Auth::user()->creatorId()) {
            return redirect()->back()->with('error', 'Permission denied');
        }
        $zapierHook->is_active = ! $zapierHook->is_active;
        $zapierHook->save();

        return redirect()->back();
    }

    public function storeApiToken(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abilities' => 'nullable|string',
        ]);

        $abilities = ['*'];
        if (! empty($validated['abilities'])) {
            $abilities = array_values(array_filter(array_map('trim', explode(',', $validated['abilities']))));
        }

        $user = User::findOrFail(Auth::user()->creatorId());
        $token = $user->createToken($validated['name'], $abilities);

        return redirect()->back()->with('success', 'Token created: '.$token->plainTextToken);
    }

    public function destroyApiToken(PersonalAccessToken $token)
    {
        if ((string) $token->tokenable_type !== User::class || (int) $token->tokenable_id !== (int) Auth::user()->creatorId()) {
            return redirect()->back()->with('error', 'Permission denied');
        }

        $token->delete();

        return redirect()->back()->with('success', 'Token revoked');
    }
}
