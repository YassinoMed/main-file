@extends('layouts.admin')
@section('page-title')
    {{ __('Production Order') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item"><a href="{{ route('production.orders.index') }}">{{ __('Production Orders') }}</a></li>
    <li class="breadcrumb-item">#{{ $order->order_number }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('edit production order')
            <a href="#" data-size="xl" data-url="{{ route('production.orders.edit', $order->id) }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" title="{{ __('Edit') }}" data-title="{{ __('Edit Production Order') }}"
                class="btn btn-sm btn-primary">
                <i class="ti ti-pencil"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Details') }}</h5>
                    <div class="mb-2"><b>{{ __('Order') }}:</b> #{{ $order->order_number }}</div>
                    <div class="mb-2"><b>{{ __('Product') }}:</b> {{ $order->product?->name }}</div>
                    <div class="mb-2"><b>{{ __('Planned Qty') }}:</b> {{ $order->quantity_planned }}</div>
                    <div class="mb-2"><b>{{ __('Produced Qty') }}:</b> {{ $order->quantity_produced }}</div>
                    <div class="mb-2"><b>{{ __('Warehouse') }}:</b> {{ $order->warehouse?->name }}</div>
                    <div class="mb-2"><b>{{ __('Work Center') }}:</b> {{ $order->workCenter?->name }}</div>
                    <div class="mb-2"><b>{{ __('Employee') }}:</b> {{ $order->employee?->name }}</div>
                    <div class="mb-2"><b>{{ __('Priority') }}:</b> {{ ucfirst($order->priority) }}</div>
                    <div class="mb-2"><b>{{ __('Status') }}:</b> {{ ucfirst(str_replace('_', ' ', $order->status)) }}</div>
                    <div class="mb-2"><b>{{ __('Planned Start') }}:</b> {{ $order->planned_start_date }}</div>
                    <div class="mb-2"><b>{{ __('Planned End') }}:</b> {{ $order->planned_end_date }}</div>
                    <div class="mb-2"><b>{{ __('Notes') }}:</b> {{ $order->notes }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5 class="mb-3">{{ __('Materials') }}</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Component') }}</th>
                                    <th>{{ __('Required') }}</th>
                                    <th>{{ __('Reserved') }}</th>
                                    <th>{{ __('Consumed') }}</th>
                                    <th>{{ __('Remaining') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->materials as $move)
                                    <tr>
                                        <td>{{ $move->component?->name }}</td>
                                        <td>{{ $move->required_qty }}</td>
                                        <td>{{ $move->reserved_qty }}</td>
                                        <td>{{ $move->consumed_qty }}</td>
                                        <td>{{ max(0, (float) $move->required_qty - (float) $move->consumed_qty) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body table-border-style">
                    <h5 class="mb-3">{{ __('Operations') }}</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Seq') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Work Center') }}</th>
                                    <th>{{ __('Planned Minutes') }}</th>
                                    <th>{{ __('Actual Minutes') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->operations as $op)
                                    <tr>
                                        <td>{{ $op->sequence }}</td>
                                        <td>{{ $op->name }}</td>
                                        <td>{{ $op->workCenter?->name }}</td>
                                        <td>{{ $op->planned_minutes }}</td>
                                        <td>{{ $op->actual_minutes }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $op->status)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body table-border-style">
                    <h5 class="mb-3">{{ __('Quality Checks') }}</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Check Point') }}</th>
                                    <th>{{ __('Result') }}</th>
                                    <th>{{ __('Operation') }}</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Checked At') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->qualityChecks as $qc)
                                    <tr>
                                        <td>{{ $qc->check_point }}</td>
                                        <td>{{ ucfirst($qc->result) }}</td>
                                        <td>{{ $qc->operation?->name }}</td>
                                        <td>{{ $qc->employee?->name }}</td>
                                        <td>{{ $qc->checked_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
