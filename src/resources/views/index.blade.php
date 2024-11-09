@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="login-status" content="{{ Auth::check() ? 'true' : 'false' }}">

@if(isset($message))
<div class="alert alert-warning">
  <p>{{ $message }}</p>
</div>
@endif

<form id="search__form" class="header__right" action="{{ route('shops.search') }}" method="get">
  <div class="header__sort">
    <label class="select-box__label sort__label">
      <select name="sort" class="select-box__item sort__item">
        <option value="random" {{ request('sort') == 'random' ? 'selected' : '' }}>ランダム</option>
        <option value="high_rating" {{ request('sort') == 'high_rating' ? 'selected' : '' }}>評価が高い順</option>
        <option value="low_rating" {{ request('sort') == 'low_rating' ? 'selected' : '' }}>評価が低い順</option>
      </select>
    </label>
  </div>
  
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
        <img id="searchIcon" src="search.png" alt="searchアイコン" />
        <input type="text" name="keyword" class="search__item-input" placeholder="Search..." value="{{ request('keyword') }}">
      </label>
    </div>
  </div>
</form>
@endsection


@section('content')

<div class="shop__wrap">
  @foreach ($shops as $shop)
  <div class="shop__content">
    @foreach($shop->images as $image)
    <img src="{{ $image->shop_image_url}}" alt="{{ $shop->name }}" class="shop__image">
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
        <div class="stage">
          <button class="heart {{ $shop->is_favorite ? 'heart-active' : 'heart' }}" data-shop-id="{{ $shop->id }}" aria-label="お気に入り" type="button" onclick="toggleFavorite(this, {{ $shop->id }})"></button>
        </div>
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
<script>
  window.searchIconPath = "{{ asset('search.png') }}"; // グローバル変数として定義
</script>
<script src="{{ asset('js/search.js') }}"></script>
<script src="{{ asset('js/toggleFavorite.js') }}"></script>
@endsection
