@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/request_login.css') }}">
@endsection


@section('content')
  <div class="box effect">
    <h2>アカウントをお持ちでない方</h2>
    <h3><a href="{{ route('register') }}" class="btn btn-solid" ><span>Registration</span></a></h3>
  </div>


  <div class="box effect">
    <h2>アカウントをお持ちの方</h2>
    <h3><a href="{{ route('login') }}" class="btn btn-solid" ><span>Login</span></a></h3>
  </div>


@endsection