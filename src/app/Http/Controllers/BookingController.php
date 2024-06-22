<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
  /**
   * マイページを表示する.
   *
   * @return \Illuminate\View\View
   */
  public function showMyPage()
  {
    $user = Auth::user();
    $bookings = Booking::where('user_id', $user->id)
      ->where('status', 'active')
      ->get();
    $histories = Booking::where('user_id', $user->id)
      ->where('status', 'completed')
      ->get();
    $shops = Shop::all(); // お気に入り店舗用のデータ
    $favorites = $user->favorites->pluck('shop_id')->toArray(); // お気に入り店舗のIDを取得

    return view('my_page', compact('bookings', 'histories', 'shops', 'favorites'));
  }

  /**
   * 予約を編集するためのページを表示する.
   *
   * @param  \App\Models\Booking  $booking
   * @return \Illuminate\View\View
   */
  public function edit(Booking $booking)
  {
    return view('bookings.edit', compact('booking'));
  }

  /**
   * 予約を更新する.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Booking  $booking
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(Request $request, Booking $booking)
  {
    $validatedData = $request->validate([
      // ここでバリデーションルールを定義
      'date' => 'required|date',
      'time' => 'required',
      'number_of_people' => 'required|integer',
      // 他のフィールドに応じてバリデーションルールを追加
    ]);

    $booking->update($validatedData);

    return redirect()->route('mypage')->with('success', '予約を更新しました');
  }

  /**
   * 予約をキャンセルする.
   *
   * @param  \App\Models\Booking  $booking
   * @return \Illuminate\Http\RedirectResponse
   */
  public function destroy(Booking $booking)
  {
    $booking->delete();

    return redirect()->route('mypage')->with('success', '予約をキャンセルしました');
  }

  /public function favorite(Shop $shop)
{
    $user = Auth::user();
    $user->favorite($shop);

    return redirect()->route('mypage')->with('success', 'お気に入りを追加しました');
}

public function unfavorite(Shop $shop)
{
    $user = Auth::user();
    $user->unfavorite($shop);

    return redirect()->route('mypage')->with('success', 'お気に入りを削除しました');
}
}
