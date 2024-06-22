<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function showMyPage()
    {
        $user = Auth::user();
        $reservations = Reservation::where('user_id', $user->id)->where('status', 'active')->get();
        $histories = Reservation::where('user_id', $user->id)->where('status', 'completed')->get();
        $shops = Shop::all(); // お気に入り店舗用のデータ
        $favorites = $user->favorites->pluck('shop_id')->toArray(); // お気に入り店舗のIDを取得

        return view('my_page', compact('reservations', 'histories', 'shops', 'favorites'));
    }

    public function edit(Reservation $reservation)
    {
        // 編集ロジック
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()->route('mypage')->with('success', '予約をキャンセルしました');
    }

    public function unfavorite(Shop $shop)
    {
        $user = Auth::user();
        $user->favorites()->detach($shop->id);

        return redirect()->route('mypage')->with('success', 'お気に入りを削除しました');
    }
}
