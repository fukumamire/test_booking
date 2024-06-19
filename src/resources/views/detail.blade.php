@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="container">
  <div class="shop-detail">
    <div class="header-container">
      <a href="#" class="header__back"><</a>
      <h1 class="shop-name">選択された飲食店名</h1>
    </div>
    <img src="#" alt="イメージ画像" class="shop-image">
    <p class="shop-tags">#地域#ジャンル</p>
    <p class="shop-description">飲食店の概要</p>
  </div>

  <div class="reservation-form">
    <form id="reservationForm" action="" method="post" class="@guest not-authenticated @endguest">
      @csrf
      <h2 class="reservation-title">予約</h2>

      <input type="date" name="date" class="form-input" value="{{ request()->is('*edit*')? $reservation->date : '' }}" id="datePicker">
      <select name="time" class="form-input">
        <option value="" disabled selected>-- 時間を選択してください --</option>
        @foreach (['18:30','19:00','19:30','20:00', '20:30', '21:00', '21:30', '22:00'] as $time)
        <option value="{{ $time }}" {{ request()->is('*edit*') && $time == date('H:i', strtotime($reservation->time)) ? 'selected' : '' }}>{{ $time }}</option>
        @endforeach
      </select>
      <select name="number" class="form-input">
        <option value="" disabled selected>--人数を選択してください --</option>
        @foreach (range(1, 10) as $number)
        <option value="{{ $number }}" {{ request()->is('*edit*') && $number == $reservation->number ? 'selected' : '' }}>{{ $number }}人</option>
        @endforeach
      </select>

      <div class="reservation-summary">
        <p>Shop: </p>
        <p>Date: <span id="dateSummary"></span></p>
        <p>Time: <span id="timeSummary"></span></p>
        <p>Number: <span id="numberSummary"></span></p>
      </div>

      <button type="submit" class="reservation-button">予約する</button>
    </form>
  </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/reservation.js') }}"></script>
@endsection