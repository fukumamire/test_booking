@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/shop-manager/reservations.css') }}">
@endsection

@section('content')
<div class="reservations-container">
  <h1 class="reservations-title">予約情報</h1>
  <table class="reservations-table">
    <thead>
      <tr>
        <th>予約ID</th>
        <th>予約日時</th>
        <th>人数</th>
        <th>状態</th>
      </tr>
    </thead>
    <tbody>
      @foreach($bookings as $booking)
      <tr>
        <td>{{ $booking->id }}</td>
        <td>{{ $booking->date }} {{ $booking->time }}</td>
        <td>{{ $booking->number_of_people }}人</td>
        <td>{{ $booking->status }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  {{ $bookings->links() }}
</div>
@endsection