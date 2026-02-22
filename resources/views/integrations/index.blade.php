@extends('layouts.admin')

@section('page-title')
    {{ __('Integrations') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Integrations') }}</li>
@endsection

@section('content')
    @php
        $webhookEvents = \App\Models\Integrations\Webhook::getEvents();
        $webhookMethods = \App\Models\Integrations\Webhook::getMethods();
        $zapierEvents = \App\Models\Integrations\ZapierHook::getEvents();
        $providers = \App\Models\Integrations\Integration::getAvailableProviders();
        $supportedProviders = ['google', 'microsoft', 'slack'];
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Connected Apps') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach ($supportedProviders as $provider)
                            @if (isset($providers[$provider]))
                                <form method="POST" action="{{ route('integrations.connect') }}">
                                    @csrf
                                    <input type="hidden" name="provider" value="{{ $provider }}">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Connect') }} {{ $providers[$provider]['name'] }}
                                    </button>
                                </form>
                            @endif
                        @endforeach
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Provider') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($integrations as $integration)
                                    <tr>
                                        <td>{{ ucfirst($integration->provider) }}</td>
                                        <td>{{ $integration->is_active ? __('Active') : __('Inactive') }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('integrations.disconnect') }}">
                                                @csrf
                                                <input type="hidden" name="provider" value="{{ $integration->provider }}">
                                                <button type="submit" class="btn btn-danger btn-sm">{{ __('Disconnect') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-muted">{{ __('No integrations connected.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('API Tokens') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('integrations.tokens.store') }}" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Abilities (comma separated)') }}</label>
                            <input name="abilities" class="form-control" placeholder="* or customers.read,products.read,invoices.read,employees.read">
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Create Token') }}</button>
                    </form>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Abilities') }}</th>
                                    <th>{{ __('Last Used') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($apiTokens as $token)
                                    <tr>
                                        <td>{{ $token->name }}</td>
                                        <td>{{ is_array($token->abilities) ? implode(', ', $token->abilities) : $token->abilities }}</td>
                                        <td>{{ $token->last_used_at ? $token->last_used_at->format('Y-m-d H:i') : '-' }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('integrations.tokens.destroy', $token->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">{{ __('Revoke') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">{{ __('No tokens created.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Webhooks') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('integrations.webhooks.store') }}" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('URL') }}</label>
                            <input name="url" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Method') }}</label>
                            <select name="method" class="form-select" required>
                                @foreach ($webhookMethods as $method)
                                    <option value="{{ $method }}">{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Events') }}</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($webhookEvents as $eventKey => $eventLabel)
                                    <label class="form-check">
                                        <input class="form-check-input" type="checkbox" name="events[]" value="{{ $eventKey }}">
                                        <span class="form-check-label">{{ $eventLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Secret (optional)') }}</label>
                            <input name="secret" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Create Webhook') }}</button>
                    </form>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('URL') }}</th>
                                    <th>{{ __('Events') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($webhooks as $webhook)
                                    <tr>
                                        <td>{{ $webhook->name }}</td>
                                        <td>{{ $webhook->url }}</td>
                                        <td>{{ is_array($webhook->events) ? implode(', ', $webhook->events) : '' }}</td>
                                        <td>{{ $webhook->is_active ? __('Active') : __('Inactive') }}</td>
                                        <td class="d-flex gap-2">
                                            <form method="POST" action="{{ route('integrations.webhooks.toggle', $webhook->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-secondary btn-sm">{{ __('Toggle') }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('integrations.webhooks.destroy', $webhook->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">{{ __('Delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-muted">{{ __('No webhooks configured.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Zapier Hooks') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('integrations.zapier.store') }}" class="mb-4">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-lg-4 mb-3">
                                <label class="form-label">{{ __('Name') }}</label>
                                <input name="name" class="form-control" required>
                            </div>
                            <div class="col-12 col-lg-5 mb-3">
                                <label class="form-label">{{ __('Hook URL') }}</label>
                                <input name="hook_url" class="form-control" required>
                            </div>
                            <div class="col-12 col-lg-3 mb-3">
                                <label class="form-label">{{ __('Event') }}</label>
                                <select name="event" class="form-select" required>
                                    @foreach ($zapierEvents as $eventKey => $eventLabel)
                                        <option value="{{ $eventKey }}">{{ $eventLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Create Zapier Hook') }}</button>
                    </form>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Event') }}</th>
                                    <th>{{ __('URL') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($zapierHooks as $hook)
                                    <tr>
                                        <td>{{ $hook->name }}</td>
                                        <td>{{ $hook->event }}</td>
                                        <td>{{ $hook->hook_url }}</td>
                                        <td>{{ $hook->is_active ? __('Active') : __('Inactive') }}</td>
                                        <td class="d-flex gap-2">
                                            <form method="POST" action="{{ route('integrations.zapier.toggle', $hook->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-secondary btn-sm">{{ __('Toggle') }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('integrations.zapier.destroy', $hook->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">{{ __('Delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-muted">{{ __('No Zapier hooks configured.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

