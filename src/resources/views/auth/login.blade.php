@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')
<body class="login-page">
  <div class="login-container">
    <div class="login-header">
      <h2>Login</h2>
    </div>
    <form action="/login" method="post" class="login-form">
      @csrf
      <div class="form-group">
        <i class="fas fa-envelope fa-2x"></i>
        <input type="email" id="email" name="email" placeholder="email" value="{{ old('email') }}">
        @error('email')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-group">
        <i class="fas fa-lock fa-2x"></i>
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
