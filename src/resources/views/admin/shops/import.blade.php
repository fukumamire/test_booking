@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/shops/shops_import.css') }}">
@endsection

@section('content')
<div class="container">
  <h1>Shop Import</h1>

  @if ($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <div class="alert alert-info">
    <h4>注意事項</h4>
    <ul>
      <li>CSVをインポートすることで、店舗情報を追加することができます。</li>
      <li>すべての項目は入力必須です。</li>
      <li>店舗名：50文字以内</li>
      <li>地域：「東京都」「大阪府」「福岡県」のいずれか</li>
      <li>ジャンル：「寿司」「焼肉」「イタリアン」「居酒屋」「ラーメン」のいずれか</li>
      <li>店舗概要：400文字以内</li>
      <li>画像URL：jpeg、pngのみアップロード可能</li>
    </ul>
  </div>

  <form method="POST" action="{{ route('shops.import') }}" enctype="multipart/form-data">
    @csrf
    <div>
      <label for="file">CSV File:</label>
      <p></p>
      <input type="file" name="file" required>
    </div>
    <button type="submit">Import</button>
  </form>
</div>
@endsection