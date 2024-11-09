@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shop_reviews.css') }}">
@endsection

@section('content')
<div class="review__wrap">
  <div class="review__header">
    口コミ
  </div>

  <div class="review__content-wrap">
    <div class="review__content shop__data">
      <div class="review__title shop-image__wrap">
        @if($shop->images->isNotEmpty())
          <img class="shop__image" src="{{ $shop->images->first()->shop_image_url }}" alt="イメージ写真">
        @else
          <img class="shop__image" src="{{ asset('default-shop-image.jpg') }}" alt="デフォルトイメージ">
        @endif
      </div>
      <div class="review__area review__detail">
        <p class="shop__name">{{ $shop->name }}</p>
        <span class="rating__name">総合：</span>
        <span class="rating__star" data-rate="{{ number_format($avgRating ?? 0,1) }}"></span>
        <span class="rating__number">{{ number_format($avgRating ?? 0,1) }}</span>
        <p class="shop__detail">{{ $shop->outline }}</p>
      </div>
    </div>

    @if(is_array($shopReviews) || $shopReviews instanceof \Illuminate\Support\Collection)
    @if($shopReviews->count() > 0)
    @foreach ($shopReviews as $shopReview)
    <div class="review__container">
    @if(Auth::check() && Auth::user()->isSuperAdmin())
        <form action="/review/delete/{{ $shopReview->id }}" method="post" class="delete-form">
          @csrf
          <button type="submit" class="delete-form__button" onclick="return confirm('本当に口コミを削除しますか？')">口コミを削除</button>
        </form>
      @endif
  
      <div class="review__content">
        <div class="review__title review__title--vertical-center">
          評価
        </div>
        <div class="review__area">
          <span class="rating__star" data-rate="{{ number_format($shopReview->rating,1) }}"></span>
          <span class="rating__number">{{ number_format($shopReview->rating,1) }}</span>
        </div>
      </div>

      <div class="review__content">
        <div class="review__title">
          本文
        </div>
        <div class="review__area">
          <p class="review__comment">{{ $shopReview->comment }}</p>
        </div>
      </div>

      @if ($shopReview->image_url)
      <div class="review__image-area">
        <a href="{{ $shopReview->image_url }}">
          <img src="{{ $shopReview->image_url }}" alt="" class="review__image">
        </a>
      </div>
      @endif
    </div>
    @endforeach
    @else
    <p>レビューはまだありません。</p>
    @endif
    @else
    <p>レビューのデータが正しくありません。</p>
    @endif
  </div>
</div>
{{-- <script src="{{ asset('js/detail.js') }}"></script> --}}
@endsection