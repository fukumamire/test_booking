@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="container">
  <div class="shop-detail">
    <div class="header-container">
      <a href="{{ $backRoute }}" class="header__back">&lt;</a>
    <h1 class="shop-name">{{ $shop->name }}</h1>
      <a href="{{ route('shop.reviews', $shop->id) }}">この店舗のレビューを見る</a>
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
    <a href="{{ route('review.create', ['shop' => $shop->id]) }}" class="review-write-link">口コミを投稿する</a>
  </div>

  <div class="reservation-form">
    @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form id="reservationForm" data-login-url="{{ route('request_login') }}" action="{{ route('bookings.store') }}" method="post" class="@guest not-authenticated @endguest">
  @csrf
  <input type="hidden" name="shop_id" value="{{ $shop->id }}">
  <h2 class="reservation-title">予約</h2>

  <input type="date" name="date" class="form-input-date" id="datePicker">
  <select name="time" class="form-input">
    <option value="" disabled selected>-- 時間を選択してください --</option>
    @foreach (['18:30','19:00','19:30','20:00', '20:30', '21:00', '21:30', '22:00'] as $time)
    <option value="{{ $time }}">{{ $time }}</option>
    @endforeach
  </select>
  <select name="number_of_people" class="form-input">
    <option value="" disabled selected>--人数を選択してください --</option>
    @foreach (range(1, 10) as $number)
    <option value="{{ $number }}">{{ $number }}人</option>
    @endforeach
  </select>
  <div class="reservation-summary">
    <p>Shop<span id="shopSummary" class="shopSummary">{{ $shop->name }}</span></p>
    <p>Date<span id="dateSummary" class="date-summary"></span></p>
    <p>Time<span id="timeSummary" class="timeSummary"></span></p>
    <p>Number<span id="numberSummary" class="numberSummary"></span></p>
  </div>

  <button type="submit" class="reservation-button">予約する</button>
</form>
  </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/reservation.js') }}"></script>
@endsection
