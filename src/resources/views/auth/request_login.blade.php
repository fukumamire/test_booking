@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/request_login.css') }}">
@endsection


@section('content')
  <div class="box effect1">
    アカウントをお持ちでない方
    <h3>Registration</h3>
  </div>


<div class="box effect1">
    アカウントをお持ちの方
    <h3>Login</h3>
  </div>


@endsection