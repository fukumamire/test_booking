@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('header')
    <form class="header__right" action="/" method="get">
    <div class="header__search">
        <label class="select-box__label">
            <select name="area" class="select-box__item">
                <option value="">All area</option>
            </select>
        </label>

        <label class="select-box__label">
            <select name="genre" class="select-box__item">
                <option value="">All genre</option>
            </select>
        </label>

        <div class="search__item">
            <div class="search__item-button"></div>
            <label class="search__item-label">
              <img src="search.png" alt="searchアイコン"/> 
              <input type="text" name="word" class="search__item-input" placeholder="Search...">
            </label>
        </div>
    </div>
</form>
<button class="heart"></button>
@endsection




