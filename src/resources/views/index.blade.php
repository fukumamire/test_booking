@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('header')
<form id="search__form" class="header__right" action="{{ route('shops.search') }}" method="get">
  <div class="header__search">
    <label class="select-box__label">
      <select name="area" class="select-box__item">
        <option value="">All area</option>
        @foreach ($areas ?? [] as $area)
        <option class="select-box__option" value="{{ $area->id }}" {{ request('area') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
        @endforeach
      </select>
    </label>

    <label class="select-box__label">
      <select name="genre" class="select-box__item">
        <option value="">All genre</option>
        @foreach ($genres ?? [] as $genre)
        <option class="select-box__option" value="{{ $genre->name }}" {{ request('genre') == $genre->name ? 'selected' : '' }}>{{ $genre->name }}</option>
        @endforeach
      </select>
    </label>

    <div class="search__item">
      <div class="search__item-button"></div>
      <label class="search__item-label">
        <img src="search.png" alt="searchアイコン" />
        <input type="text" name="word" class="search__item-input" placeholder="Search..." value="{{ request('word') }}">
      </label>
    </div>
  </div>
</form>
@endsection

@section('content')
<div class="shop__wrap">
  @if(session('message'))
  <div class="alert alert-warning">
    {{ session('message') }}
  </div>
  @endif

  @foreach ($shops as $shop)
  <div class="shop__content">
    @foreach($shop->images as $image)
    <img src="{{ $image->shop_image_url }}" alt="{{ $shop->name }}" class="shop__image">
    @endforeach
    <div class="shop__item">
      <h2 class="shop__title">{{ $shop->name }}</h2>
      <div class="shop__tag">
        @foreach($shop->areas as $area)
        <p class="shop__tag-info">#{{ $area->name }}</p>
        @endforeach
        @foreach($shop->genres as $genre)
        <p class="shop__tag-info">#{{ $genre->name }}</p>
        @endforeach
      </div>
      <div class="shop__button">
        <a href="{{ route('shop.detail', ['shop' => $shop->id]) }}" class="shop__button-detail">詳しくみる</a>
        <button class="heart {{ $shop->is_favorite? 'heart-active' : 'heart' }}" data-shop-id="{{ $shop->id }}" aria-label="お気に入り" onclick="toggleFavorite(this, {{ $shop->id }}"></button>
      </div>
    </div>
  </div>
  @endforeach
</div>

@if($shops->count())
{{ $shops->links() }} <!-- ページネーションリンク -->
@endif
</div>
@endsection

@section('script')
<script src="{{ asset('js/search.js') }}"></script>
<script src="{{ asset('js/index.js') }}"></script>
<script src="{{ asset('js/toggleFavorite.js') }}"></script>
@endsection
