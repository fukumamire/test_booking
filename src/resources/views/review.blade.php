@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/review.css') }}">
@endsection

@section('content')
<div class="review-container">
  <h2 class="review-header">飲食店レビューを書く</h2>
  <p class="review-note">来店前のレビュー投稿はご遠慮ください</p>

  <form action="{{ route('review.store', $shop->id) }}" method="post" class="review-form" enctype="multipart/form-data">
    @csrf

    <div class="shop-info">
    <h3 class="shop-name">{{ $shop->name }}</h3>
    <p class="shop-details">
        エリア:
        @foreach ($shop->areas as $area)
            {{ $area->name }}{{ $loop->last ? '' : ', ' }}
        @endforeach
        ジャンル:
        @foreach ($shop->genres as $genre)
            {{ $genre->name }}{{ $loop->last ? '' : ', ' }}
        @endforeach
    </p>
    </div>


    <div class="review-rating">
      <p class="rating-label">評価：</p>
      <div class="stars">
        @for ($i = 5; $i >= 1 ; $i--)
        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required>
        <label for="star{{ $i }}">★</label>
        @endfor
      </div>
      @error('rating')
      <span class="error">{{ $message }}</span>
      @enderror
    </div>

    <div class="review-title">
      <label for="title">タイトル：<span>(任意)</span>
        <input type="text" id="title" name="title" value="{{ old('title') }}" maxlength="20">
        @error('title')
        <span class="error">{{ $message }}</span>
        @enderror
    </div>

    <div class="review-body">
      <label for="comment">本文：<span>(必須)</span>
        <textarea id="comment" name="comment" minlength="20" maxlength="400" required>{{ old('comment') }}</textarea>
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('.review-form').addEventListener('submit', function(e) {
        var comment = document.getElementById('comment');
        if (comment.value.length < 20) {
            alert('本文は20文字以上でなければなりません。');
            e.preventDefault();
        }
    });
});
</script>