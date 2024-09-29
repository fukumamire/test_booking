@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/shop-manager/edit-shop.css') }}">
@endsection

@section('content')
<div class="edit-shop-container">
  <h1 class="edit-shop-title">店舗情報更新</h1>
  <form action="{{ route('shop-manager.shops.update', $shop->id) }}" method="POST" class="edit-shop-form" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
    <div class="form-group">
      <label for="name" class="form-label">店舗名</label>
      <input type="text" id="name" name="name" class="form-input" value="{{ $shop->name }}" required>
    </div>
    <div class="form-group">
      <label for="areas" class="form-label">エリア</label>
      <select id="areas" name="area_ids[]" class="form-select" multiple required>
        @foreach($areas as $area)
        <option value="{{ $area->id }}" {{ in_array($area->id, $shop->areas->pluck('id')->toArray()) ? 'selected' : '' }}>
          {{ $area->name }}
        </option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label for="genres" class="form-label">ジャンル</label>
      <select id="genres" name="genre_ids[]" class="form-select" multiple required>
        @foreach($genres as $genre)
        <option value="{{ $genre->id }}" {{ in_array($genre->id, $shop->genres->pluck('id')->toArray()) ? 'selected' : '' }}>
          {{ $genre->name }}
        </option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label for="outline" class="form-label">概要</label>
      <textarea id="outline" name="outline" class="form-textarea" required>{{ $shop->outline }}</textarea>
    </div>
    <div class="form-group">
      <label for="images">店舗画像</label>
      <input type="file" class="form-control-file" id="image1" name="images[]" accept=".jpg,.jpeg,.png,.gif,.svg">
      <input type="file" class="form-control-file" id="image2" name="images[]" accept=".jpg,.jpeg,.png,.gif,.svg">
      <input type="file" class="form-control-file" id="image3" name="images[]" accept=".jpg,.jpeg,.png,.gif,.svg">
      <small id="imagesHelp" class="form-text text-muted">複数選択可（jpg, jpeg, png, gif, svg形式）</small>
    </div>
    <button type="submit" class="submit-button">更新</button>
  </form>
</div>
@endsection