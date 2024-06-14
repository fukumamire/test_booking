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
                @foreach ($areas?? [] as $area)
                    <option class="select-box__option" value="{{ $area->id }}" {{ request('area') == $area->id? 'selected' : '' }}>{{ $area->name }}</option>
                @endforeach
            </select>
        </label>


        <label class="select-box__label">
            <select name="genre" class="select-box__item">
                <option value="">All genre</option>
                @foreach ($genres?? [] as $genre)
                    <option class="select-box__option" value="{{ $genre->name }}" {{ request('genre') == $genre->name? 'selected' : '' }}>{{ $genre->name }}</option>
                @endforeach
            </select>
        </label>


        <div class="search__item">
            <div class="search__item-button"></div>
            <label class="search__item-label">
                <img src="search.png" alt="searchアイコン"/>
                <input type="text" name="word" class="search__item-input" placeholder="Search..." value="{{ request('word') }}">
            </label>
        </div>
    </div>
</form>

@endsection

@section('content')
<div class="shop__wrap">
    @foreach ($shops?? [] as $shop)
        <div class="shop__content">
            <img src="{{ $shop->image_url }}" alt="{{ $shop->name }}" class="shop__image">
            <div class="shop__item">
                <h2 class="shop__title">{{ $shop->name }}</h2>
                <div class="shop__tag">
                    <p class="shop__tag-info">#{{ $shop->area }}</p>
                    <p class="shop__tag-info">#{{ $shop->genre }}</p>
                </div>
                <div class="shop__button">
                    <a href="#" class="shop__button-detail">詳しくみる</a>
                    <button class="shop__button-favorite-btn {{ $shop->is_favorite ? 'favorite' : 'not-favorite' }}"></button>
										<button class="heart"></button>
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
@endsection
