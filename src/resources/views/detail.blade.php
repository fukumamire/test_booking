@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="container">
  <div class="shop-detail">
    <div class="header-container">
      <a href="{{ $backRoute }}" class="header__back"><</a>
      <h1 class="shop-name">{{ $shop->name }}</h1>
    </div>
    
    <div class="shop-images">
      @foreach($shop->images as $image)
      <img src="{{ asset($image->shop_image_url)}}" alt="イメージ画像" class="shop-image">
      @endforeach
    </div>
    
    <p class="shop-tags">
      @foreach($shop->areas as $index => $area)
        #{{ $area->name }}
        @if($index < $shop->areas->count() - 1)
          ,
        @endif
      @endforeach
      @foreach($shop->genres as $index => $genre)
        #{{ $genre->name }}
        @if($index < $shop->genres->count() - 1)
          ,
        @endif
      @endforeach
    </p>
    <p class="shop-description">{{ $shop->outline }}</p>
  </div>

  <form id="bookingForm" data-login-url="{{ route('request_login') }}" action="{{ route('bookings.store') }}" method="post" class="@guest not-authenticated @endguest">
    @csrf
    <input type="hidden" name="shop_id" value="{{ $shop->id }}">
    <h2 class="booking-title">予約</h2>

    <label for="datePicker">日付</label>
    <input type="date" name="date" class="form-input-date" value="{{ request()->is('*edit*')? $booking->date : '' }}" id="datePicker">

    <label for="timeSelect">時間</label>
    <select name="time" id="timeSelect" class="form-input">
        <option value="" disabled selected>-- 時間を選択してください --</option>
        @foreach (['18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00'] as $time)
            <option value="{{ $time }}" {{ request()->is('*edit*') && $time == date('H:i', strtotime($booking->time)) ? 'selected' : '' }}>{{ $time }}</option>
        @endforeach
    </select>

    <label for="numberSelect">人数</label>
    <select name="number" id="numberSelect" class="form-input">
        <option value="" disabled selected>-- 人数を選択してください --</option>
        @foreach (range(1, 10) as $number)
            <option value="{{ $number }}" {{ request()->is('*edit*') && $number == $booking->number ? 'selected' : '' }}>{{ $number }}人</option>
        @endforeach
    </select>

    <div class="booking-summary">
        <p>ショップ: {{ $shop->name }}</p>
        <p>日付: <span id="dateSummary"></span></p>
        <p>時間: <span id="timeSummary"></span></p>
        <p>人数: <span id="numberSummary"></span></p>
    </div>

    <button type="submit" class="booking-button">予約する</button>
</form>

@section('script')
<script src="{{ asset('js/booking.js') }}"></script>
@endsection
