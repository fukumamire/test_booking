<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Favorite;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
// use App\Services\QrCodeService;

class BookingController extends Controller
{
  /**
   * マイページ全体を表示する.
   *
   * @return \Illuminate\View\View
   */
  public function showMyPage()
  {
    $user = Auth::user();
    // 予約情報と予約変更履歴を取得し、さらにそれぞれの予約に関連する店舗情報も取得
    $bookings = Booking::with(['changes', 'shop']) // 予約情報と予約変更履歴、および各予約の店舗情報をロード
      ->where('user_id', $user->id)
      ->where('status', 'active')
      ->get();

    // ユーザーのお気に入りの店舗を直接取得
    $favoriteShops = Favorite::with('shop', 'shop.areas', 'shop.genres')->where('user_id', $user->id)->get();

    return view('mypage.my_page', compact('bookings', 'favoriteShops'));
  }

  public function store(Request $request)
  {
    // ログインチェック
    if (!Auth::check()) {
      return redirect()->route('request_login');
    }

    $validatedData = $request->validate([
      'date' => 'required|date',
      'time' => 'required|date_format:H:i',
      'number_of_people' => 'required|integer',
      'shop_id' => 'required|exists:shops,id', // shop_id が shops テーブルに存在するかを確認
    ], [
      'date.required' => '予約日は必須です。',
      'time.required' => '予約時間は必須です。',
      'time.date_format' => '予約時間の形式が正しくありません。',
      'number_of_people.required' => '人数は必須です。',
      'number_of_people.integer' => '人数は整数で入力してください。',
      'shop_id.required' => '店舗を選択してください。',
      'shop_id.exists' => '選択された店舗は存在しません。',
    ]);

    // 予約の作成
    $booking = new Booking;
    $booking->user_id = Auth::id(); // ログインユーザーのIDを設定
    $booking->shop_id = $request->shop_id; // リクエストから shop_id を設定
    $booking->date = $request->date;
    $booking->time = $request->time;
    $booking->number_of_people = $request->number_of_people;
    $booking->status = Booking::STATUS_ACTIVE; // 予約状態を 'active' に設定
    // QRコード用のトークンを生成して予約情報に保存
    $booking->qr_code_token = base64_encode(random_bytes(16)); //例としてランダムなバイト列をエンコード
    $booking->save();

    // 予約成功後のリダイレクト
    return redirect()->route('done');
  }

  // 予約変更のメソッド
  public function update(Request $request, Booking $booking)
  {
    $request->validate([
      'date' => 'required|date',
      'time' => 'required',
      'number_of_people' => 'required|integer|min:1',
    ]);

    DB::transaction(function () use ($request, $booking) { // トランザクションを開始
      // 予約を更新する前に、変更前の情報を取得
      $oldDate = $booking->date;
      $oldTime = $booking->time;
      $oldNumberOfPeople = $booking->number_of_people;

      // 予約情報を更新
      $booking->update($request->only(['date', 'time', 'number_of_people']));

      // 予約変更履歴を保存
      $booking->changes()->create([
        'user_id' => auth()->id(),
        'old_booking_date' => $oldDate,
        'old_booking_time' => $oldTime,
        'old_number_of_people' => $oldNumberOfPeople,
        'new_booking_date' => $request->date,
        'new_booking_time' => $request->time,
        'new_number_of_people' => $request->number_of_people,
        'changed_at' => now(), // 現在の日時を設定
      ]);
    });

    return back()->with('success', '予約を変更しました。');
  }
  /**
   * 予約をキャンセルする
   */
  public function destroy(Booking $booking)
  {
    try {
      $booking->delete();
      return redirect()->route('mypage')->with('success', '予約をキャンセルしました');
    } catch (\Exception $e) {
      return redirect()->route('mypage')->with('error', '予約のキャンセルに失敗しました。もう一度試してください。');
    }
  }

  // 指定して予約した詳細　マイページ
  public function show(Booking $booking)
  {
    return view('mypape.my_page', compact('booking'));
  }
}
