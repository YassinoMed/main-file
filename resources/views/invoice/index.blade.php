@extends('layouts.admin')
@section('page-title')
    {{__('Manage Invoices')}}
@endsection
@push('script-page')
    <script>
        function copyToClipboard(element) {

            var copyText = element.id;
            navigator.clipboard.writeText(copyText);
            // document.addEventListener('copy', function (e) {
            //     e.clipboardData.setData('text/plain', copyText);
            //     e.preventDefault();
            // }, true);
            //
            // document.execCommand('copy');
            show_toastr('success', 'Url copied to clipboard', 'success');
        }

        (function () {
            var listUrl = @json(route('invoice.filters.list'));
            var saveUrl = @json(route('invoice.filters.save'));
            var applyUrlTemplate = @json(route('invoice.filters.apply', '___ID___'));
            var deleteUrlTemplate = @json(route('invoice.filters.delete', '___ID___'));
            var csrfToken = @json(csrf_token());

            function buildApplyUrl(id) {
                return applyUrlTemplate.replace('___ID___', encodeURIComponent(id));
            }

            function buildDeleteUrl(id) {
                return deleteUrlTemplate.replace('___ID___', encodeURIComponent(id));
            }

            function refreshSavedFilters() {
                $.ajax({
                    url: listUrl,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        var $select = $('#invoiceSavedFilters');
                        var current = $select.val();
                        $select.empty();
                        $select.append($('<option>', { value: '' }).text(@json(__('Saved Filters'))));

                        var filters = (data && data.filters) ? data.filters : [];
                        filters.forEach(function (filter) {
                            if (!filter || !filter.id || !filter.name || !filter.apply_url) {
                                return;
                            }
                            var $opt = $('<option>', { value: filter.apply_url }).text(filter.name);
                            $opt.attr('data-id', filter.id);
                            $select.append($opt);
                        });

                        if (current) {
                            $select.val(current);
                        }
                    }
                });
            }

            function askFilterName(callback) {
                if (typeof Swal !== 'undefined' && Swal && Swal.fire) {
                    Swal.fire({
                        title: @json(__('Save Filter')),
                        input: 'text',
                        inputPlaceholder: @json(__('Filter name')),
                        showCancelButton: true,
                        confirmButtonText: @json(__('Save')),
                        cancelButtonText: @json(__('Cancel')),
                        inputValidator: function (value) {
                            if (!value) {
                                return @json(__('This field is required.'));
                            }
                        }
                    }).then(function (result) {
                        if (result && result.isConfirmed) {
                            callback(result.value);
                        }
                    });
                } else {
                    var name = window.prompt(@json(__('Filter name')));
                    if (name) {
                        callback(name);
                    }
                }
            }

            $(document).ready(function () {
                refreshSavedFilters();

                $('#invoiceSavedFilters').on('change', function () {
                    var url = $(this).val();
                    if (url) {
                        window.location.href = url;
                    }
                });

                $('#invoiceSaveFilterBtn').on('click', function (e) {
                    e.preventDefault();
                    askFilterName(function (name) {
                        $.ajax({
                            url: saveUrl,
                            type: 'POST',
                            dataType: 'json',
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                            data: {
                                name: name,
                                issue_date: $('[name="issue_date"]').val(),
                                customer: $('[name="customer"]').val(),
                                status: $('[name="status"]').val()
                            },
                            success: function (res) {
                                if (res && res.flag === 1) {
                                    show_toastr('success', (res.msg || @json(__('Saved.'))), 'success');
                                    refreshSavedFilters();
                                } else {
                                    show_toastr('error', (res && res.msg ? res.msg : @json(__('Unable to save.'))), 'error');
                                }
                            },
                            error: function (xhr) {
                                var msg = @json(__('Unable to save.'));
                                if (xhr && xhr.responseJSON && xhr.responseJSON.msg) {
                                    msg = xhr.responseJSON.msg;
                                }
                                show_toastr('error', msg, 'error');
                            }
                        });
                    });
                });

                $('#invoiceDeleteFilterBtn').on('click', function (e) {
                    e.preventDefault();

                    var $selected = $('#invoiceSavedFilters option:selected');
                    var id = $selected.data('id');
                    if (!id) {
                        return;
                    }

                    var runDelete = function () {
                        $.ajax({
                            url: buildDeleteUrl(id),
                            type: 'DELETE',
                            dataType: 'json',
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                            success: function (res) {
                                if (res && res.flag === 1) {
                                    show_toastr('success', (res.msg || @json(__('Deleted.'))), 'success');
                                    $('#invoiceSavedFilters').val('');
                                    refreshSavedFilters();
                                } else {
                                    show_toastr('error', (res && res.msg ? res.msg : @json(__('Unable to delete.'))), 'error');
                                }
                            },
                            error: function () {
                                show_toastr('error', @json(__('Unable to delete.')), 'error');
                            }
                        });
                    };

                    if (typeof Swal !== 'undefined' && Swal && Swal.fire) {
                        Swal.fire({
                            title: @json(__('Are You Sure?')),
                            text: @json(__('This action can not be undone. Do you want to continue?')),
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: @json(__('Yes')),
                            cancelButtonText: @json(__('Cancel'))
                        }).then(function (result) {
                            if (result && result.isConfirmed) {
                                runDelete();
                            }
                        });
                    } else {
                        if (window.confirm(@json(__('Are You Sure?')))) {
                            runDelete();
                        }
                    }
                });
            });
        })();
    </script>
@endpush


@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Invoice')}}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        <a href="{{ route('invoice.export') }}" class="btn btn-sm btn-secondary me-2" data-bs-toggle="tooltip" title="{{__('Export')}}">
            <i class="ti ti-file-export"></i>
        </a>

        @can('create invoice')
            <a href="{{ route('invoice.create', 0) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('Create')}}">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection



@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['invoice.index'], 'method' => 'GET', 'id' => 'customer_submit']) }}
                        <div class="row d-flex align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                <div class="btn-box">
                                    {{ Form::label('issue_date', __('Issue Date'),['class'=>'form-label'])}}
                                    {{ Form::date('issue_date', isset($_GET['issue_date'])?$_GET['issue_date']:'', array('class' => 'form-control month-btn','id'=>'pc-daterangepicker-1', 'placeholder' => __('Issue Date'))) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                    <div class="btn-box">
                                        {{ Form::label('customer', __('Customer'),['class'=>'form-label'])}}
                                        {{ Form::select('customer', $customer, isset($_GET['customer']) ? $_GET['customer'] : '', ['class' => 'form-control select']) }}
                                    </div>
                                </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('status', __('Status'),['class'=>'form-label'])}}
                                    {{ Form::select('status', [''=>'Select Status'] + $status,isset($_GET['status'])?$_GET['status']:'', array('class' => 'form-control select')) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('saved_filters', __('Saved Filters'),['class'=>'form-label'])}}
                                    <select id="invoiceSavedFilters" class="form-control">
                                        <option value="">{{ __('Saved Filters') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto float-end ms-2 mt-4">
                                <a href="#" class="btn btn-sm btn-primary me-1"
                                   onclick="document.getElementById('customer_submit').submit(); return false;"
                                   data-bs-toggle="tooltip" data-bs-original-title="{{ __('Apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="#" class="btn btn-sm btn-secondary me-1" id="invoiceSaveFilterBtn"
                                   data-bs-toggle="tooltip" data-bs-original-title="{{ __('Save Filter') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-device-floppy"></i></span>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-danger me-1" id="invoiceDeleteFilterBtn"
                                   data-bs-toggle="tooltip" data-bs-original-title="{{ __('Delete') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-trash"></i></span>
                                </a>
                                <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                   data-bs-original-title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off"></i></span>
                                </a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th> {{ __('Invoice') }}</th>
                                <th>{{ __('Issue Date') }}</th>
                                <th>{{ __('Due Date') }}</th>
                                <th>{{ __('Due Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                @if (Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                                    <th>{{ __('Action') }}</th>
                                @endif
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($invoices as $invoice)
                                <tr data-bulk-id="{{ $invoice->id }}">
                                    <td class="Id">
                                        <a href="{{ route('invoice.show', \Crypt::encrypt($invoice->id)) }}" class="btn btn-outline-primary">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}</a>
                                    </td>
                                    <td>{{ Auth::user()->dateFormat($invoice->issue_date) }}</td>
                                    <td>
                                        @if ($invoice->due_date < date('Y-m-d'))
                                            <p class="text-danger mt-3">
                                                {{ \Auth::user()->dateFormat($invoice->due_date) }}</p>
                                        @else
                                            {{ \Auth::user()->dateFormat($invoice->due_date) }}
                                        @endif
                                    </td>
                                    <td>{{ \Auth::user()->priceFormat($invoice->getDue()) }}</td>
                                    <td>
                                        @if ($invoice->status == 0)
                                            <span
                                                class="status_badge badge bg-secondary p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 1)
                                            <span
                                                class="status_badge badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 2)
                                            <span
                                                class="status_badge badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 3)
                                            <span
                                                class="status_badge badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 4)
                                            <span
                                                class="status_badge badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @endif
                                    </td>
                                    @if (Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                                        <td class="Action">
                                                <span>
                                                @php $invoiceID= Crypt::encrypt($invoice->id); @endphp

                                                    @can('copy invoice')
                                                        <div class="action-btn me-2">
                                                            <a href="#" id="{{ route('invoice.link.copy',[$invoiceID]) }}" class="mx-3 btn btn-sm align-items-center bg-secondary"
                                                               onclick="copyToClipboard(this)" data-bs-toggle="tooltip" title="{{__('Copy Invoice')}}" data-original-title="{{__('Copy Invoice')}}"><i class="ti ti-link text-white"></i></a>
                                                        </div>
                                                    @endcan
                                                    @can('duplicate invoice')
                                                        <div class="action-btn me-2">
                                                            {!! Form::open(['method' => 'get', 'route' => ['invoice.duplicate', $invoice->id], 'id' => 'duplicate-form-' . $invoice->id]) !!}
                                                            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-primary" data-toggle="tooltip"
                                                               data-original-title="{{ __('Duplicate') }}" data-bs-toggle="tooltip" title="Duplicate Invoice"
                                                               data-original-title="{{ __('Delete') }}"
                                                               data-confirm="You want to confirm this action. Press Yes to continue or Cancel to go back"
                                                               data-confirm-yes="document.getElementById('duplicate-form-{{ $invoice->id }}').submit();">
                                                                <i class="ti ti-copy text-white"></i>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endcan
                                                    @can('show invoice')
                                                            <div class="action-btn me-2">
                                                                    <a href="{{ route('invoice.show', \Crypt::encrypt($invoice->id)) }}"
                                                                       class="mx-3 btn btn-sm align-items-center bg-warning" data-bs-toggle="tooltip" title="Show "
                                                                       data-original-title="{{ __('Detail') }}">
                                                                        <i class="ti ti-eye text-white"></i>
                                                                    </a>
                                                                </div>
                                                    @endcan
                                                    @can('edit invoice')
                                                        @if ($invoice->status != 3 && $invoice->status != 4)
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('invoice.edit', \Crypt::encrypt($invoice->id)) }}"
                                                                   class="mx-3 btn btn-sm align-items-center  bg-info" data-bs-toggle="tooltip" title="Edit "
                                                                   data-original-title="{{ __('Edit') }}">
                                                                    <i class="ti ti-pencil text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endcan
                                                    @can('delete invoice')
                                                        <div class="action-btn ">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.destroy', $invoice->id], 'id' => 'delete-form-' . $invoice->id]) !!}
                                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger " data-bs-toggle="tooltip" title="{{__('Delete')}}"
                                                                       data-original-title="{{ __('Delete') }}"
                                                                       data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                       data-confirm-yes="document.getElementById('delete-form-{{ $invoice->id }}').submit();">
                                                                        <i class="ti ti-trash text-white"></i>
                                                                    </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                    @endcan
                                                </span>
                                        </td>
                                    @endif
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
