@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/shop-manager/create-shop.css') }}">
@endsection

@section('content')
<div class="create-shop-container">
  <h1 class="create-shop-title">新規店舗作成</h1>
  <!-- バリデーションエラー表示 -->
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- 作成成功メッセージ表示 -->
  @if(session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
  @endif
    
  <form action="{{ route('shop-manager.shops.store') }}" method="POST" class="create-shop-form" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
      <label for="name" class="form-label">店舗名</label>
      <input type="text" id="name" name="name" class="form-input" required>
    </div>
    <div class="form-group">
      <label for="areas" class="form-label">エリア</label>
      <select id="areas" name="area_ids[]" class="form-select" multiple required>
        @foreach($areas as $area)
        <option value="{{ $area->id }}">{{ $area->name }}</option>
        @endforeach
      </select>
    </div>
    
    <div class="form-group">
      <label for="genres" class="form-label">ジャンル</label>
      <select id="genres" name="genre_ids[]" class="form-select" multiple required>
        @foreach($genres as$name => $id)
        <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
      </select>
    </div>
  
    <div class="form-group">
      <label for="outline" class="form-label">概要</label>
      <textarea id="outline" name="outline" class="form-textarea" required></textarea>
    </div>

    <div class="form-group">
      <label for="images">店舗画像</label>
      <input type="file" class="form-control-file" id="image1" name="images[]" a   client_max_body_size 64m;=".jpg,.jpeg,.png,.gif,.svg">
      <input type="file" class="form-control-file" id="image2" name="images[]" accept=".jpg,.jpeg,.png,.gif,.svg">
      <input type="file" class="form-control-file" id="image3" name="images[]" accept=".jpg,.jpeg,.png,.gif,.svg">
      <small id="imagesHelp" class="form-text text-muted">複数選択可（jpg, jpeg, png, gif, svg形式）</small>
    </div>
    <button type="submit" class="submit-button">作成</button>
  </form>
  <a href="{{ route('shop-manager.dashboard') }}" >店舗代表者専用ページに戻る</a>
</div>
@endsection