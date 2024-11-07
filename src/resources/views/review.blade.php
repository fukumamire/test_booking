@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/review.css') }}">
@endsection

@section('content')
<div class="review-container">
  <h1 class="review-header">今回のご利用はいかがでしたか？</h1>

  <form action="{{ route('review.store', $shop->id) }}" method="post" class="review-form" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="shop_id" value="{{ $shop->id }}">

    <div class="review-content">
      <!-- 左列 (店舗情報) -->
      <div class="shop-info">
        <div class="shop-image-container">
          @if($shop->images->isNotEmpty())
            <img src="{{ $shop->images[0]->shop_image_url }}" alt="{{ $shop->name }}" class="shop-image">
          @endif
        </div>
        <h3 class="shop-name">{{ $shop->name }}</h3>
        <p class="shop-details">
          @foreach ($shop->areas as $area)
            #{{ $area->name }}{{ $loop->last ? '' : ', ' }}
          @endforeach 
          @foreach ($shop->genres as $genre)
          # {{ $genre->name }}{{ $loop->last ? '' : ', ' }}
          @endforeach
        </p>
        <!-- 「詳しく見る」ボタンとお気に入りボタンを追加 -->
        <div class="shop-buttons">
          <a href="{{ route('shop.detail', ['shop' => $shop->id]) }}" class="shop-button-detail">詳しくみる</a>
          <button class="heart {{ $shop->is_favorite ? 'heart-active' : 'heart' }}" data-shop-id="{{ $shop->id }}" aria-label="お気に入り" type="button" onclick="toggleFavorite(this, {{ $shop->id }})">
          </button>
        </div>
      </div>

      <!-- 右列 (口コミフォーム) -->
      <div class="review-fields">
        <div class="review-rating">
          <p class="rating-label">体験を評価してください</p>
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

        <div class="review-body">
          <label for="comment" class="comment-label">口コミを投稿</label>
          <textarea id="comment" name="comment" placeholder="カジュアルな夜のお出かけにおすすめのスポット" maxlength="400" required>{{ old('comment') }}</textarea>
          <span class="char-count">0/400(最高文字数)</span>
          @error('comment')
          <span class="error">{{ $message }}</span>
          @enderror
        </div>

        <div class="image-upload">
          <label for="image">画像の追加</label>
          <input type="file" id="image" name="image" accept=".jpeg,.png">
          <p class="image-note">クリックして写真を追加またはドラッグアンドドロップ</p>
        </div>

        <button type="submit" class="submit-button">口コミを投稿</button>
      </div>
    </div>
  </form>

  @if ($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif
</div>

@section('scripts')
{{-- お気に入りボタン --}}
<script src="{{ asset('js/toggleFavorite.js') }}"></script>

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

  const commentInput = document.getElementById('comment');
  const charCount = document.querySelector('.char-count');
  commentInput.addEventListener('input', () => {
    charCount.textContent = `${commentInput.value.length}/400`;
  });
});
</script>
@endsection
