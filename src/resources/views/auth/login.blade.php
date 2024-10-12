@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<body class="login-page">
  <div class="login-container">
    <div class="login-header">
      <h2>Login</h2>
    </div>
    <form action="{{ route('login') }}" method="post" class="login-form">
      @csrf
      <input type="hidden" name="guard" value="web">
      
      <div class="form-group">
        <img src="auth-img/mail.png" alt="mailアイコン"  width="30">
        <input type="email" id="email" name="email" placeholder="email" value="{{ old('email') }}">
        @error('email')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-group">
        <img src="auth-img/key.png" alt="keyアイコン" width="30">
        <input type="password" id="password" name="password" placeholder="password">
        @error('password')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-submit">
        <button type="submit" class="submit-button">ログイン</button>
      </div>
    </form>
    @if ($errors->any())
      <div class="alert alert-danger">
        {{ $errors->first() }}
      </div>
    @endif
  </div>
  <div class="admin-login-wrapper">
    <p class="admin-login-link"><a href="{{ route('admin.login') }}">管理者としてログインする場合はこちら</a></p>
    <p class="admin-login-link"><a href="{{ route('shop-manager.login') }}">店舗代表者としてログインする場合はこちら</a></p>
  </div>
</body>
@endsection
