@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/shop-manager/shops/index.css') }}">

@endsection
@section('content')
<h1>店舗一覧</h1>

<table>
    <thead>
        <tr>
            <th>店舗名</th>
            <th>概要</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shops as $shop)
            <tr>
                <td>{{ $shop->name }}</td>
                <td>{{ $shop->outline }}</td>
                <td>
                    <a href="{{ route('shop-manager.shops.edit', $shop->id) }}">編集</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection