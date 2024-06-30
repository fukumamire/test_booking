<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\Paginator;
use App\Models\Shop;
use App\Models\Area;
use App\Models\Genre;
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

  // お気に入りボタン

  public function favorite(Shop $shop)
  {
    if (!Auth::check()) {
      return response()->json(['error' => 'Not authenticated'], 401);
    }

    $shop->favoritedBy()->attach(Auth::user()->id);
    return response()->json(['success' => true, 'is_favorite' => true]);
  }

  public function unfavorite(Shop $shop)
  {
    if (!Auth::check()) {
      return response()->json(['error' => 'Not authenticated'], 401);
    }

    $shop->favoritedBy()->detach(Auth::user()->id);
    return response()->json(['success' => true, 'is_favorite' => false]);
  }

  // 現在の店舗がユーザーのお気に入りリストに含まれているかどうかを判定
  public function isFavorite(Request $request, Shop $shop)
  {
    if (!Auth::check()) {
      return response()->json(['error' => 'Not authenticated'], 401);
    }

    $isFavorite = $shop->favoritedBy()->where('user_id', Auth::id())->exists();
    return response()->json(['is_favorite' => $isFavorite]);
  }

  // public function toggleFavorite(Shop $shop)
  // {
  //   if (!Auth::check()) {
  //     return response()->json(['error' => 'Not authenticated'], 401);
  //   }

  //   $shop->toggleFavorite();
  //   return response()->json(['success' => true, 'is_favorite' => $shop->isFavoriteBy(Auth::user())]);
  // }

  // public function toggleFavorite(Shop $shop)
  // {
  //   if (!Auth::check()) {
  //     return response()->json(['redirect' => url('/request_login'), 'status' => 401], 401);
  //   }

  //   $shop->toggleFavorite();
  //   return response()->json(['success' => true, 'is_favorite' => $shop->isFavoriteBy(Auth::user())]);
  // }

  // 飲食店詳細ページ

  public function detail($shopId)
  {
    $backRoute = url('/');
    $shop = Shop::find($shopId); // Eloquentを使用して店舗情報を取得
    return view('detail', compact('shop', 'backRoute'));
  }
}
