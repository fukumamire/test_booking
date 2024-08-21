@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/qr-code.css') }}">
@endsection

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="card">
      <div class="card-header">{{ __('QR Code') }}</div>

      <div class="card-body text-center">
        <img src="{{ $qrCode }}" alt="QR Code" class="img-fluid">
        <p class="mt-3">
          このQRコードを来店時にお見せください
        </p>
      </div>
    </div>
  </div>
</div>

@endsection