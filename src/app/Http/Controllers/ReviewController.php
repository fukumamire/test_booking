<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth; // Authファサードをインポート

class ReviewController extends Controller
{
  public function store(Request $request, Shop $shop)
  {
    $request->validate([
      'rating' => 'required|integer|min:1|max:5',
      'title' => 'nullable|string|max:20',
      'comment' => 'required|string|min:20|max:400',
    ], [
      'rating.required' => '評価は必須です。',
      'comment.min' => 'コメントは20文字以上でなければなりません。',
    ]);

    $review = new Review;
    $review->shop_id = $shop->id;
    $review->user_id = Auth::id();
    $review->rating = $request->rating;
    $review->title = $request->title;
    $review->comment = $request->comment;
    $review->save();

    return back()->with('success', 'レビューが正常に提出されました。');
  }
}
