@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('header')
<form class="header__right" action="{{ route('shops.search') }}" method="get">
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
<button class="heart"></button>
@endsection

@section('content')
<div>
    @foreach (($shops?? []) as $shop)
        <div>
            <h2>{{ $shop->name }}</h2>
            <p>{{ $shop->outline }}</p>
            <!-- ショップの詳細を表示 -->
        </div>
    @endforeach
    @if(isset($shops))
        {{ $shops->links() }} <!-- ページネーションリンク -->
    @endif
</div>
@endsection

@section('script')
  <script src="{{ asset('js/search.js') }}"></script>
@endsection
