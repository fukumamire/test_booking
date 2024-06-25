@extends('layouts.app')

@section('content')
<div class="container">
  <!-- バリデーションエラーの表示 -->
  @if ($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <!-- 操作の結果を通知 -->
  @if (session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
  @endif

  <p class="user__name">{{ Auth::user()->name }}さん</p>
  <div class="mypage__wrap">
    <div class="booking__wrap">
      <div class="booking__tab">
        <label class="booking__title hover__color--blue">
          <input type="radio" name="tab" class="booking__title-input" checked>
          予約状況
        </label>
        <div class="booking__content-wrap">
          @foreach ($bookings ?? '' as $booking)
          @include('partials.bookings', ['booking' => $booking, 'loopIteration' => $loop->iteration])
          @endforeach
        </div>

        {{-- <label class="booking__title hover__color--steelblue">
          <input type="radio" name="tab" class="booking__title-input">
          予約履歴
        </label>
        <div class="booking__content-wrap">
          @foreach ($histories as $booking)
          @include('partials.booking_history', ['booking' => $booking, 'loopIteration' => $loop->iteration])
          @endforeach
        </div> --}}

        <label class="booking__title hover__color--orange mobile-favorite__title">
          <input type="radio" name="tab" class="booking__title-input">お気に入り店舗
        </label>
        <div class="booking__content-wrap mobile-favorite__wrap">
          @foreach ($shops as $shop)
          @include('partials.shop', ['shop' => $shop, 'favorites' => $favorites])
          @endforeach
        </div>
      </div>
    </div>

    <div class="favorite__wrap">
      <p class="favorite__title">お気に入り店舗</p>
      <div class="shop__wrap">
        @foreach ($shops as $shop)
        @include('partials.shop', ['shop' => $shop, 'favorites' => $favorites])
        @endforeach
      </div>
    </div>
  </div>
  @endsection