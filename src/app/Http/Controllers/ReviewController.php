<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;

use App\Http\Requests\StoreReviewRequest;


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
    $isShopManager = Auth::check() && Auth::user()->roles->contains('name', 'shop-manager');

    $review = $userHasReview ? $shop->reviews()->where('user_id', Auth::id())->first() : null;

    return view('review', [
      'shop' => $shop,
      'userHasReview' => $userHasReview,
      'review' => $review,
      'isShopManager' => $isShopManager
    ]);
  }

  public function store(StoreReviewRequest $request)
  {
    if (Auth::check() && Auth::user()->roles->contains('name', 'shop-manager')) {
      return redirect()->back()->withErrors(['shop_manager_error' => '店舗代表者は口コミを投稿できません。']);
    }

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
    } else {
      // 新しいレビューを作成
      $review = new Review;
      $review->shop_id = $shop->id;
      $review->user_id = Auth::id();
      $review->rating = $request->input('rating');
      $review->comment = $request->input('comment');
    }
    if ($request->hasFile('image_url')) {
      $path = $request->file('image_url')->store('public/reviews');
      $review->image_url = basename($path);
    }
    $review->save();
    // avg_rating を更新
    $shop->updateAvgRating();

    return redirect()->route('shop.reviews', ['shop' => $shop->id])->with('success', 'レビューが正常に提出されました。');
  }


  public function destroy(
    $shopId,
    $reviewId
  ) {
    $review = Review::findOrFail($reviewId);
    $user = Auth::guard('admin')->user() ?: Auth::user();

    // ログイン中のユーザーが管理者（super-admin）かどうかを確認
    $isSuperAdmin = $user ? $user->hasRole('super-admin') : false;

    // 管理者または口コミの投稿者本人であれば削除を許可
    if ($isSuperAdmin || $user->id === $review->user_id) {
      // 画像ファイルがある場合、削除
      if ($review->image_url) {
        Storage::disk('public')->delete('public/reviews/' . $review->image_url);
      }
      $review->delete();
      return redirect()->route('shop.detail', ['shop' => $shopId])->with('success', '口コミが削除されました。');
    }

    return redirect()->back()->with('error', '権限がありません。');
  }
}
