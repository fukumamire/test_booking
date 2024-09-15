@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<body class="login-page">
  
  <div class="login-container">
    <div class="login-header">
      <h2>Administrator Login</h2>
    </div>
    <form action="{{ route('admin.login.submit') }}" method="post" class="login-form">
      @csrf
      <!-- 管理者用のhidden inputを追加 -->
      <input type="hidden" name="guard" value="admin">
      
      <div class="form-group">
        <img src="{{ asset('auth-img/mail.png') }}" alt="mailアイコン"  width="30">
        <input type="email" id="email" name="email" placeholder="email" value="{{ old('email') }}">
        @error('email')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-group">
        <img src="{{ asset('auth-img/key.png') }}" alt="keyアイコン" width="30">
        <input type="password" id="password" name="password" placeholder="password">
        @error('password')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-submit">
        <button type="submit" class="submit-button">ログイン</button>
      </div>
    </form>
  </div>
</body>
@endsection
