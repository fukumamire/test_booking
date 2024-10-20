@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/shop-manager/dashboard.css') }}">
@endsection

@section('content')

<div class="container">
  <h1 class="shop-manager__title">店舗代表者専用ページ</h1>

  <nav class="shop-manager__nav">
    <ul>
      <li><a href="{{ route('shop-manager.shops.index') }}" >店舗一覧</a></li>
      <li><a href="{{ route('shop-manager.shops.create') }}" >新規店舗作成</a></li>
      <li><a href="{{ route('shop-manager.reservations') }}" >予約一覧</a></li>
    </ul>
  </nav>
</div>

@endsection