@extends('layouts.admin')

@section('page-title')
    {{ __('Workflow Executions') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('workflows.index') }}">{{ __('Workflows') }}</a></li>
    <li class="breadcrumb-item">{{ __('Executions') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex gap-2">
        <a href="{{ route('workflows.show', $workflow->id) }}" class="btn btn-sm btn-secondary">
            {{ __('Back') }}
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Model') }}</th>
                                    <th>{{ __('Triggered By') }}</th>
                                    <th>{{ __('Created') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($executions as $execution)
                                    <tr>
                                        <td>{{ $execution->id }}</td>
                                        <td>{{ $execution->status }}</td>
                                        <td>{{ class_basename($execution->model_type) }} #{{ $execution->model_id }}</td>
                                        <td>{{ $execution->triggered_by ?? '-' }}</td>
                                        <td>{{ $execution->created_at ? $execution->created_at->format('Y-m-d H:i') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-muted">{{ __('No executions found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $executions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

