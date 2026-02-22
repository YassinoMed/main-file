@extends('layouts.admin')

@section('page-title')
    {{ __('Edit Workflow') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('workflows.index') }}">{{ __('Workflows') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit') }}</li>
@endsection

@section('content')
    @php
        $condition = is_array($workflow->trigger_conditions) ? ($workflow->trigger_conditions[0] ?? []) : [];
        $action = is_array($workflow->actions) ? ($workflow->actions[0] ?? []) : [];
        $actionData = $action['data'] ?? [];
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('workflows.update', $workflow->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label">{{ __('Name') }}</label>
                                <input type="text" name="name" class="form-control" value="{{ $workflow->name }}" required>
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label">{{ __('Trigger') }}</label>
                                <select name="trigger_model" class="form-select" required>
                                    @foreach ($triggers as $key => $trigger)
                                        <option value="{{ $key }}" {{ $workflow->trigger_model === $key ? 'selected' : '' }}>
                                            {{ $trigger['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">{{ __('Description') }}</label>
                                <textarea name="description" class="form-control" rows="3">{{ $workflow->description }}</textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $workflow->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ __('Active') }}</label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">{{ __('Condition (optional)') }}</h6>
                        <div class="row">
                            <div class="col-12 col-lg-4 mb-3">
                                <label class="form-label">{{ __('Field') }}</label>
                                <input type="text" name="trigger_conditions[0][field]" class="form-control" value="{{ $condition['field'] ?? '' }}">
                            </div>
                            <div class="col-12 col-lg-4 mb-3">
                                <label class="form-label">{{ __('Operator') }}</label>
                                <select name="trigger_conditions[0][operator]" class="form-select">
                                    @php $operator = $condition['operator'] ?? 'equals'; @endphp
                                    <option value="equals" {{ $operator === 'equals' ? 'selected' : '' }}>{{ __('equals') }}</option>
                                    <option value="not_equals" {{ $operator === 'not_equals' ? 'selected' : '' }}>{{ __('not_equals') }}</option>
                                    <option value="contains" {{ $operator === 'contains' ? 'selected' : '' }}>{{ __('contains') }}</option>
                                    <option value="greater_than" {{ $operator === 'greater_than' ? 'selected' : '' }}>{{ __('greater_than') }}</option>
                                    <option value="less_than" {{ $operator === 'less_than' ? 'selected' : '' }}>{{ __('less_than') }}</option>
                                    <option value="is_empty" {{ $operator === 'is_empty' ? 'selected' : '' }}>{{ __('is_empty') }}</option>
                                    <option value="is_not_empty" {{ $operator === 'is_not_empty' ? 'selected' : '' }}>{{ __('is_not_empty') }}</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-4 mb-3">
                                <label class="form-label">{{ __('Value') }}</label>
                                <input type="text" name="trigger_conditions[0][value]" class="form-control" value="{{ $condition['value'] ?? '' }}">
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">{{ __('Action') }}</h6>
                        <div class="row">
                            <div class="col-12 col-lg-4 mb-3">
                                <label class="form-label">{{ __('Type') }}</label>
                                <select name="actions[0][type]" class="form-select" required>
                                    @foreach ($actions as $key => $availableAction)
                                        <option value="{{ $key }}" {{ ($action['type'] ?? '') === $key ? 'selected' : '' }}>
                                            {{ $availableAction['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label">{{ __('Email To') }}</label>
                                <input type="text" name="actions[0][data][to]" class="form-control" value="{{ $actionData['to'] ?? '' }}">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label">{{ __('Email Subject') }}</label>
                                <input type="text" name="actions[0][data][subject]" class="form-control" value="{{ $actionData['subject'] ?? '' }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">{{ __('Email Body / Webhook Body / Message') }}</label>
                                <textarea name="actions[0][data][body]" class="form-control" rows="4">{{ $actionData['body'] ?? '' }}</textarea>
                            </div>

                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label">{{ __('Notification User ID') }}</label>
                                <input type="text" name="actions[0][data][user_id]" class="form-control" value="{{ $actionData['user_id'] ?? '' }}">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label">{{ __('Notification Message') }}</label>
                                <input type="text" name="actions[0][data][message]" class="form-control" value="{{ $actionData['message'] ?? '' }}">
                            </div>

                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label">{{ __('Task Title') }}</label>
                                <input type="text" name="actions[0][data][title]" class="form-control" value="{{ $actionData['title'] ?? '' }}">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label">{{ __('Assign To') }}</label>
                                <input type="text" name="actions[0][data][assign_to]" class="form-control" value="{{ $actionData['assign_to'] ?? '' }}">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label">{{ __('Due Date') }}</label>
                                <input type="text" name="actions[0][data][due_date]" class="form-control" value="{{ $actionData['due_date'] ?? '' }}">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label">{{ __('Update Field') }}</label>
                                <input type="text" name="actions[0][data][field]" class="form-control" value="{{ $actionData['field'] ?? '' }}">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label">{{ __('Update Value') }}</label>
                                <input type="text" name="actions[0][data][value]" class="form-control" value="{{ $actionData['value'] ?? '' }}">
                            </div>

                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label">{{ __('Webhook URL') }}</label>
                                <input type="text" name="actions[0][data][url]" class="form-control" value="{{ $actionData['url'] ?? '' }}">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label">{{ __('Webhook Method') }}</label>
                                <input type="text" name="actions[0][data][method]" class="form-control" value="{{ $actionData['method'] ?? '' }}">
                            </div>

                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label">{{ __('List ID') }}</label>
                                <input type="text" name="actions[0][data][list_id]" class="form-control" value="{{ $actionData['list_id'] ?? '' }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('workflows.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
