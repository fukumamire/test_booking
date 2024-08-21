@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/reservation_failure.css') }}">
@endsection

@section('content')

<body class="done-page">
  <div class="content__wrap">
    <h2>予約が確認できました。</h2>
    <p class="content__text">
      ご来店ありがとうごさいます。
    </p>
    <a class="content__button" href="/">戻る</a>
  </div>
</body>
@endsection