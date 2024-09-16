@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_index.css') }}">
@endsection

@section('content')

<body>
  <div class="container">
    <p class="user__name">管理者専用ページ</p>
    <nav class="admin-nav__content">
      <ul class="admin-nav__item">
        <li class="admin-nav__list">
          <a href="/admin/user/index" class="admin-nav__link">ユーザー一覧</a>
        </li>
        <li class="admin-nav__list">
          <a href="/admin/email-notification" class="admin-nav__link">お知らせメール作成・送信</a>
        </li>
        <li class="admin-nav__list">
          <a href="{{ route('users.create-shop-manager') }}" class="admin-nav__link">店舗代表者作成</a>
        </li>
        <li class="admin-nav__list">
          <a href="" class="admin-nav__link">新規店舗追加</a>
        </li>
      </ul>
    </nav>
  </div>
</body>
@endsection