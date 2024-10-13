@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/shop-manager/shops/index.css') }}">
@endsection

@section('content')
<h1>店舗一覧</h1>
<table class="shops-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>店舗名</th>
      <th>概要</th>
      <th>エリア</th>
      <th>ジャンル</th>
      <th>状態</th>
      <th>操作</th>
    </tr>
  </thead>
  <tbody>
    @foreach($shops as $shop)
    <tr>
      <td>{{ $shop->id }}</td>
      <td>{{ $shop->name }}</td>
      <td>{{ Str::limit($shop->outline, 50) }}</td>
      <td>
        @foreach($shop->areas as $area)
        {{ $area->name }},
        @endforeach
      </td>
      <td>
        @foreach($shop->genres as $genre)
        {{ $genre->name }},
        @endforeach
      </td>
      <td>
        @if($shop->trashed())
        削除済み
        @else
        有効
        @endif
      </td>
      <td>
        @if(!$shop->trashed())
        <a href="{{ route('shop-manager.shops.destroy', $shop->id) }}" onclick="event.preventDefault();
                            document.getElementById('delete-form-{{ $shop->id }}').submit();" class="btn btn-danger">
          Delete
        </a>

        <form id="delete-form-{{ $shop->id }}" action="{{ route('shop-manager.shops.destroy', $shop->id) }}" method="POST" style="display: none;">
          @csrf
          @method('DELETE')
        </form>
        @else
        <a href="{{ route('shop-manager.shops.restore', $shop->id) }}" onclick="event.preventDefault();
                            document.getElementById('restore-form-{{ $shop->id }}').submit();" class="btn btn-success">
          Restore
        </a>

        <form id="restore-form-{{ $shop->id }}" action="{{ route('shop-manager.shops.restore', $shop->id) }}" method="POST" style="display: none;">
          @csrf
        </form>
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
</div>
@endsection