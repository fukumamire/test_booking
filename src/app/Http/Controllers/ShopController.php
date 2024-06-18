<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\Paginator;
use App\Models\Shop;
use App\Models\Area;
use App\Models\Genre;
use Illuminate\Http\Request;

class ShopController extends Controller
{
  public function search(Request $request)
  {
    $query = Shop::query();

    // キーワードによるフィルタリング
    if ($request->filled('keyword')) {
      $query->where(function ($query) use ($request) {
        $query->where('name', 'like', '%' . $request->keyword . '%')
          ->orWhere('outline', 'like', '%' . $request->keyword . '%');
      });
    }

    // エリアでのフィルタリング
    if ($request->filled('area')) {
      $query->whereHas('areas', function ($q) use ($request) {
        $q->where('areas.id', $request->area);
      });
    }

    // ジャンルでのフィルタリング
    if ($request->filled('genre')) {
      $query->whereHas('genres', function ($q) use ($request) {
        $q->where('genres.name', $request->genre);
      });
    }

    // areas と genres のリレーションをロードして shops を取得
    $shops = $query->with(['areas', 'genres'])->paginate(10);

    // エリアとジャンルのリストを取得
    $areas = Area::all();
    $genres = Genre::select('name')->distinct()->get();

    // 検索結果が空の場合はエラーメッセージを設定
    $message = $shops->isEmpty() ? 'お探しの飲食店はございません。再度検索してください' : '';

    return view('index', compact('shops', 'areas', 'genres', 'message'));
  }

  public function index()
  {
    $shops = Shop::with(['areas', 'genres'])->paginate(20); // ページネーションを使用してデータを取得
    $areas = Area::all();
    $genres = Genre::select('name')->distinct()->get(); // ジャンルの名前を選択し、重複を除去して取得
    return view('index', compact('shops', 'areas', 'genres')); // 'index'ビューに渡すデータを準備
  }

  // 飲食店詳細ページ
  // public function detail(Request $request)
  // {
  //   $user = Auth::user();
  //   $userId = Auth::id();
  //   $shop = Shop::find($request->shop_id);
  //   $review = Review::where('user_id', $userId)->where('shop_id', $shop->id)->first();
  //   $from = $request->input('from');
  //   // 戻りルートを設定
  //   $backRoute = match ($from) {
  //     'mypage' => '/mypage',
  //     default => '/'
  //   };

  //   return view('detail', compact('user', 'shop', 'review', 'avgRating', 'countFavorites', 'backRoute'));
  // }
}
