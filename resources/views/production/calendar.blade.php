@extends('layouts.admin')

@section('page-title')
    {{ __('Production Calendar') }}
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.css') }}">
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item">{{ __('Calendar') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Calendar') }}</h5>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('production.calendar.data') }}",
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {
                    (function() {
                        var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                            },
                            buttonText: {
                                timeGridDay: "{{ __('Day') }}",
                                timeGridWeek: "{{ __('Week') }}",
                                dayGridMonth: "{{ __('Month') }}"
                            },
                            themeSystem: 'bootstrap',
                            initialDate: '{{ $transdate }}',
                            slotDuration: '00:10:00',
                            navLinks: true,
                            selectable: true,
                            selectMirror: true,
                            editable: false,
                            dayMaxEvents: true,
                            handleWindowResize: true,
                            events: data,
                        });
                        calendar.render();
                    })();
                }
            });
        });
    </script>
@endpush

