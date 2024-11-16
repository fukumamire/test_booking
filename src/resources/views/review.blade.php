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
            @for ($i = 5; $i >= 1 ; $i--)
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
          <textarea id="comment" name="comment" placeholder="カジュアルな夜のお出かけにおすすめのスポット" maxlength="400" required>
            @if(isset($review))
              {{ $review->comment }}
            @else
              {{ old('comment') }}
            @endif
          </textarea>
          @error('comment')
            <span class="error">{{ $message }}</span>
          @enderror
          <span class="char-count">0/400(最高文字数)</span>
        </div>

        

        {{-- <div class="image-upload">
          <label for="image">画像の追加</label>
          <input type="file" id="image" name="image" accept=".jpeg,.png">
          <p class="image-note">クリックして写真を追加またはドラッグアンドドロップ</p>
        </div>
      </div>  
    </div>
  </form> --}}
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        <div class="button__content">
          @if ($userHasReview)
          <button type="submit" class="submit-button">口コミを編集</button>
          @else
          <button type="submit" class="submit-button">口コミを投稿</button>
          @endif
        </div>
      </div>

      @section('scripts')
      {{-- お気に入りボタン --}}
      {{-- <script src="{{ asset('js/toggleFavorite.js') }}"></script> --}}
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
          // 星の色を変更する関数
          function changeStarColor(selectedIndex) {
            labels.forEach((label, index) => {
              // selectedIndexより右側も含めて青にする
              if (index >= selectedIndex) {
                label.style.color = '#007bff'; // 青に設定
              } else {
                label.style.color = '#ddd'; // グレーに設定
              }
            });
          }
          // ホバーアクション時の星の色を変更する関数
          labels.forEach((label, index) => {
            label.addEventListener('mouseover', () => {
              hoverStar(index);
            });
            label.addEventListener('mouseout', () => {
              resetStars();
            });
          });
          // ホバー時の色変更
          function hoverStar(hoverIndex) {
            labels.forEach((label, index) => {
              if (index >= hoverIndex) {
                label.style.color = '#007bff'; // 青に設定
              } else {
                label.style.color = '#ddd'; // グレーに設定
              }
            });
          }
          // 星の色をリセットする関数
          function resetStars() {
            const checkedStar = document.querySelector('.stars input:checked');
            if (checkedStar) {
              changeStarColor(Array.prototype.indexOf.call(stars, checkedStar));
            } else {
              labels.forEach(label => {
                label.style.color = '#ddd'; // グレーにリセット
              });
            }
          }
          // 初期化時に既存の評価を反映させる
          const checkedStar = document.querySelector('.stars input:checked');
          if (checkedStar) {
            changeStarColor(Array.prototype.indexOf.call(stars, checkedStar));
          }
        
            // 初期値の設定(★評価)
          const ratingValue = document.querySelector('input[name="rating"]:checked')?.value || 5;
          stars.forEach((star, index) => {
            if (index < ratingValue) {
            star.checked = true;
            }
          });

          // コメント入力の文字数カウント
          const commentInput = document.getElementById('comment');
          const charCount = document.querySelector('.char-count');
          commentInput.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = `${length}/400`;
            if (length < 20) {
              charCount.style.color = 'red';
            } else {
              charCount.style.color = 'black';
            }
          });
          // 初期値の設定
          commentInput.dispatchEvent(new Event('input'));
        });
      </script>