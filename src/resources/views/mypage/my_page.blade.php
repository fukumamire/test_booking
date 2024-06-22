@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/my_page.css') }}">
@endsection
<p class="user__name">
  {{-- {{ Auth::user()->name }} --}}
  さん
</p>

<div class="mypage__wrap">
  <!-- Reservation Section -->
  <div class="reservation__wrap">
    <div class="reservation__tab">
      <!-- Reservation Status Tab -->
      <label class="reservation__title hover__color--blue">
        <input type="radio" name="tab" class="reservation__title-input" checked>
        予約状況
      </label>
      <div class="reservation__content-wrap">
        @foreach ($reservations as $reservation)
          @include('partials.reservation', ['reservation' => $reservation, 'loopIteration' => $loop->iteration])
        @endforeach
      </div>

      <!-- Reservation History Tab -->
      <label class="reservation__title hover__color--steelblue">
        <input type="radio" name="tab" class="reservation__title-input">
        予約履歴
      </label>
      <div class="reservation__content-wrap">
        @foreach ($histories as $reservation)
          @include('partials.reservation_history', ['reservation' => $reservation, 'loopIteration' => $loop->iteration])
        @endforeach
      </div>

      <!-- お気に入り店舗　タグ -->
      <label class="reservation__title hover__color--orange mobile-favorite__title">
        <input type="radio" name="tab" class="reservation__title-input">お気に入り店舗
      </label>
      <div class="reservation__content-wrap mobile-favorite__wrap">
        @foreach ($shops as $shop)
          @include('partials.shop', ['shop' => $shop, 'favorites' => $favorites])
        @endforeach
      </div>
    </div>
  </div>

  <!-- お気に入り店舗情報 -->
  <div class="favorite__wrap">
    <p class="favorite__title">お気に入り店舗</p>
    <div class="shop__wrap">
      @foreach ($shops as $shop)
        @include('partials.shop', ['shop' => $shop, 'favorites' => $favorites])
      @endforeach
    </div>
  </div>
</div>
