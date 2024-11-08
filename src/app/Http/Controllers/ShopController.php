<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use App\Models\Shop;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Favorite;
use App\Models\Review; // Reviewクラスのインポートを追加
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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
    $message = $shops->isEmpty() ? 'お探しの飲食店はございません。再度検索してください。' : null;

    return view('index', compact('shops', 'areas', 'genres', 'message'));
  }

  public function index()
  {
    $shops = Shop::with(['areas', 'genres', 'images'])->paginate(20);
    // エリアデータの取得
    $areas = Area::all();
    // ジャンルの名前を選択し、重複を除去して取得
    $genres = Genre::select('name')->distinct()->get();

    return view('index', compact('shops', 'areas', 'genres'));
  }


  // お気に入りをトグルするメソッド
  public function toggleFavorite(Shop $shop)
  {
    if (!Auth::check()) {
      return response()->json(['error' => 'Not authenticated'], 401);
    }

    $user = Auth::user();
    $shop->toggleFavorite($user);

    $isFavorite = $shop->isFavoriteBy($user);

    return response()->json(['success' => true, 'is_favorite' => $isFavorite]);
  }

  // 現在の店舗がユーザーのお気に入りリストに含まれているかどうかを判定
  public function isFavorite(Shop $shop)
  {
    if (!Auth::check()) {
      return response()->json(['error' => 'Not authenticated'], 401);
    }

    $isFavorite = $shop->isFavoriteBy(Auth::user());
    return response()->json(['is_favorite' => $isFavorite]);
  }


  // 飲食店詳細ページ

  public function detail($shopId)
  {
    $backRoute = url('/');
    $shop = Shop::find($shopId); // Eloquentを使用して店舗情報を取得
    return view('detail', compact('shop', 'backRoute'));
  }


  //特定の店舗のレビューを取得してビューに渡すメソッド


  public function showReviews(Shop $shop)
  {
    $avgRating = $shop->reviews()->avg('rating');
    $shopReviews = $shop->reviews()->latest()->get();

    return view('shop_reviews', [
      'shop' => $shop,
      'avgRating' => $avgRating,
      'shopReviews' => $shopReviews
    ]);
  }
}
