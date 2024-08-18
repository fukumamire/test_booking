@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/two-factor-challenge.css') }}">
@endsection

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">{{ __('Two Factor Authentication') }}</div>

        <div class="card-body">
          @if (session('status') === 'two-factor-authentication-enabled')
          <div class="alert alert-success" role="alert">
            Two Factor Authentication has been enabled.
          </div>
          @endif

          <form method="POST" action="{{ route('two-factor.login') }}">
            @csrf

            <div class="form-group row">
              <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Authentication Code') }}</label>

              <div class="col-md-6">
                <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" required autocomplete="one-time-code">

                @error('code')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>

            <div class="form-group row mb-0">
              <div class="col-md-8 offset-md-4">
                <button type="submit" class="btn btn-primary">
                  {{ __('Verify') }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection