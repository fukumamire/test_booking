@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/my_page.css') }}">
@endsection
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

  <div class="mypage__wrap">
    <div class="left__side">
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

          <label class="booking__title hover__color--steelblue">
            <input type="radio" name="tab" class="booking__title-input">
            予約履歴
          </label>
          <div class="booking__content-wrap">
            @foreach ($histories as $booking)
            @include('partials.booking_history', ['booking' => $booking, 'loopIteration' => $loop->iteration])
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <div class="right__side">
      <p class="user__name">{{ Auth::user()->name }}さん</p>
      <p class="favorite__title">お気に入り店舗</p>
      <div class="shop__wrap">
        @foreach ($favorites as $favorite)
          @php
            $shop = $shops->firstWhere('id', $favorite->shop_id);
          @endphp
          @if ($shop)
            @include('partials.shop', ['shop' => $shop, 'favorites' => $favorites])
          @endif
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
