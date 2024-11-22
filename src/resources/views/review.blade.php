@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/review.css') }}">
@endsection

@section('content')
<div class="review-container">
  @if ($isShopManager)
    <p class="alert alert-warning">店舗代表者は口コミを投稿できません。</p>
  @else
    <form action="{{ route('shop.review.store', $shop->id) }}" method="post" class="review-form" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="shop_id" value="{{ $shop->id }}">

      <div class="review-content">
        <!-- 左列 (店舗情報) -->
        <div class="shop-info">
          <div class="shop-image-container">
            <h1 class="review-header">今回のご利用はいかがでしたか？</h1>
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
              #{{ $genre->name }}{{ $loop->last ? '' : ', ' }}
            @endforeach
          </p>
          <div class="shop-buttons">
            <a href="{{ route('shop.detail', ['shop' => $shop->id]) }}" class="shop-button-detail">詳しくみる</a>
            <button class="heart {{ $shop->is_favorite ? 'heart-active' : 'heart' }}" data-shop-id="{{ $shop->id }}" aria-label="お気に入り" type="button" onclick="toggleFavorite(this, {{ $shop->id }})"></button>
          </div>
        </div>

        <!-- 右列 (口コミフォーム) -->
        <div class="review-fields">
          <div class="review-rating">
            <p class="rating-label">体験を評価してください</p>
            <div class="stars">
              @for ($i = 5; $i >= 1; $i--)
                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required {{ $i == ($review->rating ?? 5) ? 'checked' : '' }}>
                <label for="star{{ $i }}">★</label>
              @endfor
            </div>
            @error('rating')
              <span class="error">{{ $message }}</span>
            @enderror
          </div>

          <div class="review-body">
            <label for="comment" class="comment-label">口コミを投稿</label>
            <textarea id="comment" name="comment" placeholder="カジュアルな夜のお出かけにおすすめのスポット" maxlength="400" required>
              @isset($review) 
                {{ $review->comment }} 
              @else 
                {{ old('comment') }} 
              @endisset
            </textarea>
            @error('comment')
              <span class="alert alert-danger">{{ $message }}</span>
            @enderror
            <span class="char-count">0/400(最高文字数)</span>
          </div>

          <!-- 画像アップロードセクション -->
          <p class="review__form-title">画像の追加</p>
          @error('image_url')
            <p class="error__message">{{ $message }}</p>
          @enderror
          <div class="upload-area">
            <div class="image-area">
              <img src="@isset($review) {{ asset('storage/' . $review->image_url) }} @else '' @endisset" class="image-area__image">
            </div>
            @if (!$review || !$review->image_url)
              <div class="upload-text__area">
                <p class="upload-area__text">クリックして写真を追加</p>
                <p class="upload-area__text--small">またはドロップアンドドロップ</p>
              </div>
            @endif
            <input type="file" name="image_url" class="input-file" accept=".jpeg,.png">
          </div>
        </div>
      </div>

      <div class="button__content">
        @if ($userHasReview)
          <button type="submit" class="submit-button">口コミを編集</button>
        @else
          <button type="submit" class="submit-button">口コミを投稿</button>
        @endif
      </div>
    </form>
  @endif
</div>
@endsection


<script>
document.addEventListener("DOMContentLoaded", function() {
  const stars = document.querySelectorAll('.stars input[type="radio"]');
  const labels = document.querySelectorAll('.stars label');

  // ラジオボタンの変更イベントハンドラー
  stars.forEach((star, index) => {
    star.addEventListener('change', () => {
      changeStarColor(index);
    });
  });

  // 星の色を設定する関数
  function changeStarColor(selectedIndex) {
    labels.forEach((label, index) => {
      label.style.color = index >= selectedIndex ? '#007bff' : '#ddd'; // 青とグレー
    });
  }

  // ホバーアクション時の星の色を変更する関数
  labels.forEach((label, index) => {
    label.addEventListener('mouseover', () => {
      hoverStar(index);
    });
    label.addEventListener('mouseout', resetStars);
  });

  // ホバー時の色変更
  function hoverStar(hoverIndex) {
    labels.forEach((label, index) => {
      label.style.color = index >= hoverIndex ? '#007bff' : '#ddd'; // 青とグレー
    });
  }

  // 星の色をリセットする関数
  function resetStars() {
    const checkedStar = document.querySelector('.stars input:checked');
    if (checkedStar) {
      changeStarColor(Array.prototype.indexOf.call(stars, checkedStar));
    } else {
      labels.forEach(label => label.style.color = '#ddd'); // グレーにリセット
    }
  }

  // 初期化時に既存の評価を反映させる
  const checkedStar = document.querySelector('.stars input:checked');
  if (checkedStar) {
    changeStarColor(Array.prototype.indexOf.call(stars, checkedStar));
  }

  // コメント入力の文字数カウント
  const commentInput = document.getElementById('comment');
  const charCount = document.querySelector('.char-count');
  
  // 初期値の設定
  const initialLength = commentInput.value.length;
  charCount.textContent = `${initialLength}/400`;
  charCount.style.color = initialLength < 20 ? 'red' : 'black';

  // 入力時の更新
  commentInput.addEventListener('input', function() {
    const length = this.value.length;
    charCount.textContent = `${length}/400`;
    charCount.style.color = length < 20 ? 'red' : 'black';
  });

  // 画像アップロード機能のJavaScript
  var input = document.querySelector('.input-file');
  var imageArea = document.querySelector('.image-area');
  var textArea = document.querySelector('.upload-text__area');
  var image = document.querySelector('.image-area__image');

  input.addEventListener('change', function(e) {
    var file = e.target.files[0];
    var reader = new FileReader();

    reader.onload = function(e) {
      image.src = e.target.result;
      textArea.style.display = 'none';
      imageArea.style.display = 'block';
    }

    reader.readAsDataURL(file);
  });

  if (!image.getAttribute('src')) {
    imageArea.style.display = 'none';
  }
});
</script>
