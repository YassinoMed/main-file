@extends('layouts.admin')
@section('page-title')
    {{ __('Production Orders') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item">{{ __('Production Orders') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create production order')
            <a href="#" data-size="xl" data-url="{{ route('production.orders.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create Production Order') }}"
                class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Order') }}</th>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Planned Qty') }}</th>
                                    <th>{{ __('Produced Qty') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Priority') }}</th>
                                    <th>{{ __('Planned Start') }}</th>
                                    <th>{{ __('Planned End') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr class="font-style">
                                        <td>#{{ $order->order_number }}</td>
                                        <td>{{ $order->product?->name }}</td>
                                        <td>{{ $order->quantity_planned }}</td>
                                        <td>{{ $order->quantity_produced }}</td>
                                        <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></td>
                                        <td><span class="badge bg-info">{{ ucfirst($order->priority) }}</span></td>
                                        <td>{{ $order->planned_start_date }}</td>
                                        <td>{{ $order->planned_end_date }}</td>
                                        <td class="Action">
                                            @can('show production order')
                                                <div class="action-btn me-2">
                                                    <a href="{{ route('production.orders.show', $order->id) }}"
                                                        class="mx-3 btn btn-sm align-items-center bg-warning"
                                                        data-bs-toggle="tooltip" title="{{ __('View') }}"><i
                                                            class="ti ti-eye text-white"></i></a>
                                                </div>
                                            @endcan
                                            @can('edit production order')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bg-info"
                                                        data-url="{{ route('production.orders.edit', $order->id) }}"
                                                        data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                                                        title="{{ __('Edit') }}"
                                                        data-title="{{ __('Edit Production Order') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete production order')
                                                <div class="action-btn">
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['production.orders.destroy', $order->id],
                                                        'id' => 'delete-form-' . $order->id,
                                                    ]) !!}
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                            class="ti ti-trash text-white"></i></a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan
                                        </td>
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

