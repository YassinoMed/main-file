@extends('layouts.admin')

@section('page-title')
    {{ __('Workflow') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('workflows.index') }}">{{ __('Workflows') }}</a></li>
    <li class="breadcrumb-item">{{ __('Details') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex gap-2">
        <a href="{{ route('workflows.executions', $workflow->id) }}" class="btn btn-sm btn-secondary">
            {{ __('Executions') }}
        </a>
        <a href="{{ route('workflows.edit', $workflow->id) }}" class="btn btn-sm btn-info">
            {{ __('Edit') }}
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-2"><strong>{{ __('Name') }}:</strong> {{ $workflow->name }}</div>
                    <div class="mb-2"><strong>{{ __('Trigger') }}:</strong> {{ $workflow->trigger_model }}</div>
                    <div class="mb-2"><strong>{{ __('Active') }}:</strong> {{ $workflow->is_active ? __('Yes') : __('No') }}</div>
                    @if (!empty($workflow->description))
                        <div class="mb-2"><strong>{{ __('Description') }}:</strong> {{ $workflow->description }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Recent Executions') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Model') }}</th>
                                    <th>{{ __('Created') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($executions as $execution)
                                    <tr>
                                        <td>{{ $execution->id }}</td>
                                        <td>{{ $execution->status }}</td>
                                        <td>{{ class_basename($execution->model_type) }} #{{ $execution->model_id }}</td>
                                        <td>{{ $execution->created_at ? $execution->created_at->format('Y-m-d H:i') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">{{ __('No executions yet.') }}</td>
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

