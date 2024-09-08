@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_user.css') }}">
@endsection

@section('content')
<div class="container">
  <h1 class="page-title">ユーザー一覧</h1>
  <div class="table__wrap">
    <table class="user__table">
      <thead>
        <tr>
          <th class="table__header header-no">ID</th>
          <th class="table__header header-name">名前</th>
          <th class="table__header header-email">メールアドレス</th>
          <th class="table__header header-roles">ロール</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($users as $user)
        <tr>
          <td class="table__data">{{ $user->id }}</td>
          <td class="table__data">{{ $user->name }}</td>
          <td class="table__data data-email">{{ $user->email }}</td>
          <td class="table__data">
            @forelse ($user->roles as $role)
            {{ $role->name }}
            @empty
            ユーザー
            @endforelse
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="pagination-wrap">
    @if ($users->lastPage() > 1)
    <ul class="pagination">
      @if ($users->currentPage() > 1)
      <li><a href="{{ $users->url(1) }}">最初のページ</a></li>
      @endif
      @for ($i = 1; $i <= $users->lastPage(); $i++)
        <li class="{{ ($users->currentPage() == $i) ? ' active' : '' }}">
          <a href="{{ $users->url($i) }}">{{ $i }}</a>
        </li>
        @endfor
        @if ($users->currentPage() < $users->lastPage())
          <li><a href="{{ $users->url($users->lastPage()) }}">最後のページ</a></li>
          @endif
    </ul>
    @endif
  </div>
</div>
@endsection