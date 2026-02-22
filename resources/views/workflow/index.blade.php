@extends('layouts.admin')

@section('page-title')
    {{ __('Workflows') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Workflows') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        <a href="{{ route('workflows.create') }}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Trigger') }}</th>
                                    <th>{{ __('Active') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($workflows as $workflow)
                                    <tr>
                                        <td>{{ $workflow->name }}</td>
                                        <td>{{ $workflow->trigger_model }}</td>
                                        <td>{{ $workflow->is_active ? __('Yes') : __('No') }}</td>
                                        <td class="d-flex gap-2">
                                            <a href="{{ route('workflows.show', $workflow->id) }}" class="btn btn-warning btn-sm">
                                                {{ __('View') }}
                                            </a>
                                            <a href="{{ route('workflows.edit', $workflow->id) }}" class="btn btn-info btn-sm">
                                                {{ __('Edit') }}
                                            </a>
                                            <form method="POST" action="{{ route('workflows.toggle', $workflow->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-secondary btn-sm">{{ __('Toggle') }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('workflows.destroy', $workflow->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">{{ __('Delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">{{ __('No workflows found.') }}</td>
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

