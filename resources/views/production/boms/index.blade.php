@extends('layouts.admin')
@section('page-title')
    {{ __('Bill of Materials') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item">{{ __('Bill of Materials') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create production bom')
            <a href="#" data-size="xl" data-url="{{ route('production.boms.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create BOM') }}"
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
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Active Version') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($boms as $bom)
                                    <tr class="font-style">
                                        <td>{{ $bom->product?->name }}</td>
                                        <td>{{ $bom->name }}</td>
                                        <td>{{ $bom->code }}</td>
                                        <td>{{ $bom->activeVersion?->version }}</td>
                                        <td class="Action">
                                            @can('show production bom')
                                                <div class="action-btn me-2">
                                                    <a href="{{ route('production.boms.show', $bom->id) }}"
                                                        class="mx-3 btn btn-sm align-items-center bg-warning"
                                                        data-bs-toggle="tooltip" title="{{ __('View') }}"><i
                                                            class="ti ti-eye text-white"></i></a>
                                                </div>
                                            @endcan
                                            @can('edit production bom')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bg-info"
                                                        data-url="{{ route('production.boms.edit', $bom->id) }}"
                                                        data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                                                        title="{{ __('Edit') }}" data-title="{{ __('Edit BOM') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete production bom')
                                                <div class="action-btn">
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['production.boms.destroy', $bom->id],
                                                        'id' => 'delete-form-' . $bom->id,
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

