@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/two-factor-challenge.css') }}">
@endsection

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="card">
      <div class="card-header">{{ __('QR Code') }}</div>

      <div class="card-body text-center">
        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="img-fluid">
        <p class="mt-3">
          Please scan this QR code with your authenticator app.
        </p>
      </div>
    </div>
  </div>
</div>

@endsection