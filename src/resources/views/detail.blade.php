@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="container">
  <div class="shop-detail">
    <h1 class="shop-name">選択された飲食店名</h1>
    <img src="#" alt="イメージ画像" class="shop-image">
    <p class="shop-tags">#地域#ジャンル</p>
    <p class="shop-description">飲食店の概要</p>
  </div>

  <div class="reservation-form">
    @auth
    <form id="reservationForm" action="{{ route('reservations.store') }}" method="post">
      @csrf
      <h2 class="reservation-title">予約</h2>

      <input type="date" name="date" class="form-input" value="{{ request()->is('*edit*')? $reservation->date : '' }}" id="datePicker">
      <select name="time" class="form-input">
        <option value="" disabled selected>-- 時間を選択してください --</option>
        @foreach (['20:00', '20:30', '21:00', '21:30', '22:00'] as $time)
          <option value="{{ $time }}" {{ request()->is('*edit*') && $time == date('H:i', strtotime($reservation->time)) ? 'selected' : '' }}>{{ $time }}</option>
        @endforeach
      </select>
      <select name="number" class="form-input">
        <option value="" disabled selected>--人数を選択してください --</option>
        @foreach (range(1, 5) as $number)
          <option value="{{ $number }}" {{ request()->is('*edit*') && $number == $reservation->number ? 'selected' : '' }}>{{ $number }}人</option>
        @endforeach
      </select>

      <div class="reservation-summary">
        <p>Shop: {{ $shop->name }}</p>
        <p>Date: <span id="dateSummary">{{ request()->is('*edit*')? $reservation->date : '' }}</span></p>
        <p>Time: <span id="timeSummary">{{ request()->is('*edit*') ? date('H:i', strtotime($reservation->time)) : '' }}</span></p>
        <p>Number: <span id="numberSummary">{{ request()->is('*edit*')? $reservation->number. '人' : '' }}</span></p>
      </div>

      <button type="submit" class="reservation-button">予約する</button>
    </form>
    @else
    <a href="{{ route('request_login') }}" class="reservation-button">予約する</a>
    @endauth
  </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/reservation.js') }}"></script>
@endsection
