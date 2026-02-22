@extends('layouts.auth')

@section('page-title')
    {{ __('Confirm Password') }}
@endsection

@section('content')
    <div class="card-body">
        <div>
            <h2 class="mb-3 f-w-600">{{ __('Confirm Password') }}</h2>
        </div>

        <p class="mb-4 text-muted">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>

        {{ Form::open(['url' => 'confirm-password', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
        <div class="custom-login-form">
            <div class="form-group mb-3">
                <label class="form-label">{{ __('Password') }}</label>
                {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter Your Password'), 'required' => 'required']) }}
                @error('password')
                    <span class="error invalid-password text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="d-grid">
                {{ Form::submit(__('Confirm'), ['class' => 'btn btn-primary mt-2']) }}
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection
