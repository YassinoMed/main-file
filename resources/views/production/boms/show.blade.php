@extends('layouts.admin')
@section('page-title')
    {{ __('BOM') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item"><a href="{{ route('production.boms.index') }}">{{ __('Bill of Materials') }}</a></li>
    <li class="breadcrumb-item">{{ $bom->name }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('edit production bom')
            <a href="#" data-size="xl" data-url="{{ route('production.boms.edit', $bom->id) }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" title="{{ __('Edit') }}" data-title="{{ __('Edit BOM') }}"
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
                    <div class="mb-2"><b>{{ __('Product') }}:</b> {{ $bom->product?->name }}</div>
                    <div class="mb-2"><b>{{ __('Name') }}:</b> {{ $bom->name }}</div>
                    <div class="mb-2"><b>{{ __('Code') }}:</b> {{ $bom->code }}</div>
                    <div class="mb-2"><b>{{ __('Active Version') }}:</b> {{ $bom->activeVersion?->version }}</div>
                </div>
            </div>
            @can('edit production bom')
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('Create Version') }}</h5>
                        {{ Form::open(['route' => ['production.boms.versions.store', $bom->id], 'method' => 'POST', 'class' => 'needs-validation', 'novalidate']) }}
                        <div class="form-group">
                            {{ Form::label('version', __('Version'), ['class' => 'form-label']) }}<x-required></x-required>
                            {{ Form::text('version', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Version')]) }}
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            @endcan
        </div>
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5 class="mb-3">{{ __('Versions') }}</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Version') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Components') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bom->versions as $version)
                                    <tr>
                                        <td>{{ $version->version }}</td>
                                        <td>
                                            @if ($version->is_active)
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $version->lines?->count() ?? 0 }}</td>
                                        <td>
                                            @if (!$version->is_active)
                                                @can('edit production bom')
                                                    {{ Form::open(['route' => ['production.boms.versions.activate', $bom->id, $version->id], 'method' => 'POST', 'class' => 'd-inline']) }}
                                                    <button type="submit"
                                                        class="btn btn-sm btn-primary">{{ __('Activate') }}</button>
                                                    {{ Form::close() }}
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body table-border-style">
                    <h5 class="mb-3">{{ __('Active Version Components') }}</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Component') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Scrap %') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (($bom->activeVersion?->lines ?? collect()) as $line)
                                    <tr>
                                        <td>{{ $line->component?->name }}</td>
                                        <td>{{ $line->quantity }}</td>
                                        <td>{{ $line->scrap_percent }}</td>
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

