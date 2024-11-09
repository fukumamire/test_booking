<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;


class ReviewController extends Controller
{
  public function showReviews($shopId)
  {
    $shop = Shop::findOrFail($shopId);
    $avgRating = $shop->reviews()->avg('rating');
    $shopReviews = $shop->reviews()->latest()->get();

    return view('shop_reviews', [
      'shop' => $shop,
      'avgRating' => $avgRating,
      'shopReviews' => $shopReviews
    ]);
  }

  public function create($shopId)
  {
    $shop = Shop::findOrFail($shopId);
    // 現在のユーザーがこの店舗に対して既にレビューを投稿しているかどうかを確認
    $userHasReview = Auth::check() && $shop->reviews()->where('user_id', Auth::id())->exists();

    // ユーザーのロールをチェック
    $isShopManager = Auth::check() && Auth::user()->roles->contains('name', 'shop-manager');


    if ($userHasReview) {
      $review = $shop->reviews()->where('user_id', Auth::id())->first();
      return view('review', [
        'shop' => $shop,
        'userHasReview' => $userHasReview,
        'review' => $review,
        'isShopManager' => $isShopManager
      ]);
    } else {
      return view('review', [
        'shop' => $shop,
        'userHasReview' => $userHasReview,
        'isShopManager' => $isShopManager
      ]);
    }
  }

  public function store(Request $request)
  {
    if (Auth::check() && Auth::user()->roles->contains('name', 'shop-manager')) {
      return redirect()->back()->withErrors(['shop_manager_error' => '店舗代表者は口コミを投稿できません。']);
    }

    $request->validate([
      'rating' => 'required|integer|min:1|max:5',
      'comment' => 'required|string|min:20|max:400',
      'shop_id' => 'required|exists:shops,id',
    ], [
      'rating.required' => '評価は必須です。',
      'comment.min' => 'コメントは20文字以上でなければなりません。',
    ]);

    if (!Auth::check()) { // ユーザーがログインしていない場合
      return redirect()->route('request_login')->withErrors(['user_not_authenticated' => 'ログインしてください。']);
    }

    $shopId = $request->input('shop_id'); // リクエストから shop_id を取得
    $shop = Shop::findOrFail($shopId); // shop_id で店舗を見つける

    $review = $shop->reviews()->where('user_id', Auth::id())->first();

    if ($review) {
      // 既存のレビューを更新
      $review->rating = $request->input('rating');
      $review->comment = $request->input('comment');
      $review->save();
    } else {
      // 新しいレビューを作成
      $review = new Review;
      $review->shop_id = $shop->id;
      $review->user_id = Auth::id();
      $review->rating = $request->input('rating');
      $review->comment = $request->input('comment');
      $review->save();
    }

    return redirect()->route('shop.reviews', ['shop' => $shop->id])->with('success', 'レビューが正常に提出されました。');
  }
}
