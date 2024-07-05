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
        </div>
      </div>
    </div>

    <div class="right__side">
      <p class="user__name">{{ Auth::user()->name }}さん</p>
      <p class="favorite__title">お気に入り店舗</p>
      <div class="shop__wrap">
        @foreach($favoriteShops as $favorite)
        <div class="shop__content">
          @foreach($favorite->shop->images as $image)
          <img src="{{ $image->shop_image_url }}" alt="{{ $favorite->shop->name }}" class="shop__image">
          @endforeach
          <div class="shop__item">
            <h2 class="shop__title">{{ $favorite->shop->name }}</h2>
            <div class="shop__tag">
              @foreach($favorite->shop->areas as $area)
              <p class="shop__tag-info">#{{ $area->name }}</p>
              @endforeach
              @foreach($favorite->shop->genres as $genre)
              <p class="shop__tag-info">#{{ $genre->name }}</p>
              @endforeach
            </div>
            <div class="shop__button">
              <a href="{{ route('shop.detail', ['shop' => $favorite->shop->id]) }}" class="shop__button-detail">詳しくみる</a>
              <div class="stage">
                <button class="heart" data-shop-id="{{ $favorite->shop->id }}" aria-label="お気に入り" type="button"></button>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/toggleFavorite.js') }}"></script>
@endsection