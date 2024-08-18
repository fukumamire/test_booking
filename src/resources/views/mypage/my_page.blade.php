@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/my_page.css') }}">
<link rel="stylesheet" href="{{ asset('css/modal.css') }}">
@endsection

@section('content')
<div class="container">
  <!-- バリデーションエラーの表示 -->
  @if ($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
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
        <!-- タブ部分 -->
        <input type="radio" name="tabs" id="booking-status" checked>
        <label class="booking__title" for="booking-status">予約状況</label>
        
        <input type="radio" name="tabs" id="booking-history">
        <label class="booking__title" for="booking-history">予約履歴</label>

        <!-- 予約情報を表示するコンテナ -->
        <div id="booking-status-content" class="booking__content-wrap booking__status">
          @if (isset($bookings) && count($bookings) > 0)
          @foreach ($bookings as $booking)
          @include('partials.bookings', ['booking' => $booking])
          @endforeach
          @else
          <div class="booking__placeholder">予約はありません</div>
          @endif
        </div>

        <!-- 予約履歴を表示するコンテナ -->
        <div id="booking-history-content" class="booking__content-wrap booking__history">
          @if (isset($bookings) && count($bookings) > 0)
          @foreach ($bookings as $booking)
          @foreach ($booking->changes as $change)
          <div class="booking__change-history">
            <p>変更日: {{ $change->changed_at }}</p>
            <p>変更前: {{ $change->old_booking_date }} {{ $change->old_booking_time }} {{ $change->old_number_of_people }}人</p>
            <p>変更後: {{ $change->new_booking_date }} {{ $change->new_booking_time }} {{ $change->new_number_of_people }}人</p>
            <p>店舗名: {{ $booking->shop->name }}</p>
          </div>
          @endforeach
          @endforeach
          @else
          <div class="booking__placeholder">予約変更履歴はありません</div>
          @endif
        </div>
      </div>
    </div>{{-- partials/bookings.blade.php --}}

    <div class="right__side">
      <p class="user__name">{{ Auth::user()->name }}さん</p>
      <p class="favorite__title">お気に入り店舗</p>
      <div class="shop__wrap">
        @if (isset($favoriteShops) && count($favoriteShops) > 0)
        @foreach ($favoriteShops as $favorite)
        <div class="shop__content">
          @foreach ($favorite->shop->images as $image)
          <img src="{{ $image->shop_image_url }}" alt="{{ $favorite->shop->name }}" class="shop__image">
          @endforeach
          <div class="shop__item">
            <h2 class="shop__title">{{ $favorite->shop->name }}</h2>
            <div class="shop__tag">
              @foreach ($favorite->shop->areas as $area)
              <p class="shop__tag-info">#{{ $area->name }}</p>
              @endforeach
              @foreach ($favorite->shop->genres as $genre)
              <p class="shop__tag-info">#{{ $genre->name }}</p>
              @endforeach
            </div>
            <div class="shop__button">
              <a href="{{ route('shop.detail', ['shop' => $favorite->shop->id]) }}" class="shop__button-detail">詳しくみる</a>
              <div class="stage">
                <button class="heart heart-active" data-shop-id="{{ $favorite->shop->id }}" aria-label="お気に入り" type="button"></button>
              </div>
            </div>
          </div>
        </div>
        @endforeach
        @else
        <div class="favorite__placeholder">お気に入りの店舗はありません</div>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script src="{{ asset('js/toggleFavorite.js') }}"></script>
@endsection
