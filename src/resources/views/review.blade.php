@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/review.css') }}">
@endsection

@section('content')
<div class="review-container">
  <h1 class="review-header">飲食店レビューを書く</h1>
  <p class="review-note">来店前のレビュー投稿はご遠慮ください</p>

  <form action="{{ route('review.store', $shop->id) }}" method="post" class="review-form" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="shop_id" value="{{ $shop->id }}">
    <div class="shop-info">
      <h3 class="shop-name">{{ $shop->name }}</h3>
      <p class="shop-details">
          エリア:
          @foreach ($shop->areas as $area)
            {{ $area->name }}{{ $loop->last ? '' : ', ' }},
          @endforeach
          ジャンル:
          @foreach ($shop->genres as $genre)
            {{ $genre->name }}{{ $loop->last ? '' : ', ' }}
          @endforeach
          <br>
          {{ $shop->outline }}
      </p>

      @if($shop->images->isNotEmpty())
        @foreach ($shop->images as $image)
            <img src="{{ $image->shop_image_url }}" alt="{{ $shop->name }}" class="shop-image">
        @endforeach
      @endif
    </div>

    <div class="review-rating">
      <p class="rating-label">評価：</p>
      <div class="stars">
        @for ($i = 1; $i <= 5; $i++)
          <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required>
          <label for="star{{ $i }}">★</label>
        @endfor
      </div>
      @error('rating')
      <span class="error">{{ $message }}</span>
      @enderror
    </div>

    <div class="review-title">
      <label for="title" class="title">タイトル：<span  class="optional-label">(任意)</span></label>
        <input type="text" id="title" name="title" value="{{ old('title') }}" maxlength="20">
        @error('title')
        <span class="error">{{ $message }}</span>
        @enderror
    </div>

    <div class="review-body">
      <label for="comment" class="comment">本文：<span class="required-text">(必須)</span></label>
        <textarea id="comment" name="comment" minlength="20" maxlength="400" required>{{ old('comment') }}</textarea>
        <span class="min-length-text">20文字以上記載してください</span>
        @error('comment')
        <span class="error">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit">レビューを投稿する</button>
  </form>
</div>

@if ($errors->any())
<div class="alert alert-danger">
  <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const stars = document.querySelectorAll('.stars input[type="radio"]');
    stars.forEach(star => {
        star.addEventListener('change', () => {
            stars.forEach(s => s.nextElementSibling.classList.remove('active'));
            let currentStar = star;
            while(currentStar) {
                currentStar.nextElementSibling.classList.add('active');
                currentStar = currentStar.previousElementSibling ? currentStar.previousElementSibling.querySelector('input') : null;
            }
        });
    });
});
</script>
@endsection
