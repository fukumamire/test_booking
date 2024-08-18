@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/reservation_failure.css') }}">
@endsection

@section('content')

<body class="done-page">
  <div class="content__wrap">
    <p class="content__text">
      予約認証に失敗しました<br>
      QRコードのスキャン中に問題が発生しました。もう一度試してください。
    </p>
    <a class="content__button" href="/">戻る</a>
  </div>
</body>
@endsection