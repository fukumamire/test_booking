<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use App\Models\Shop;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Favorite;
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

  // お気に入りボタン
  // public function favorite(Shop $shop)
  // {
  //   if (!Auth::check()) {
  //     return response()->json(['error' => 'Not authenticated'], 401);
  //   }

  //   $favorite = new Favorite;
  //   $favorite->shop_id = $shop->id;
  //   $favorite->user_id = Auth::user()->id;
  //   $favorite->save();

  //   return response()->json(['success' => true, 'is_favorite' => true]);
  // }

  // public function unfavorite(Shop $shop)
  // {
  //   if (!Auth::check()) {
  //     return response()->json(['error' => 'Not authenticated'], 401);
  //   }

  //   $favorite = Favorite::where('shop_id', $shop->id)->where('user_id', Auth::user()->id)->first();
  //   if ($favorite) {
  //     $favorite->delete();
  //     return response()->json(['success' => true, 'is_favorite' => false]);
  //   }

  //   return response()->json(['error' => 'Favorite not found'], 404);
  // }

  // // 現在の店舗がユーザーのお気に入りリストに含まれているかどうかを判定
  // public function isFavorite(Request $request, Shop $shop)
  // {
  //   if (!Auth::check()) {
  //     return response()->json(['error' => 'Not authenticated'], 401);
  //   }

  //   $isFavorite = Favorite::where('shop_id', $shop->id)->where('user_id', Auth::id())->exists();
  //   return response()->json(['is_favorite' => $isFavorite]);
  // }

  // // お気に入りをトグルするメソッド
  // public function toggleFavorite(Request $request, Shop $shop)
  // {
  //   $userId = auth()->id(); // 認証済みユーザーのIDを取得

  //   if (!$userId) {
  //     return response()->json(['error' => 'Not authenticated'], 401);
  //   }

  //   return DB::transaction(function () use ($shop, $userId) {
  //     $isFavorite = Favorite::where('shop_id', $shop->id)->where('user_id', $userId)->first();

  //     if ($isFavorite) {
  //       $isFavorite->delete();
  //       return ['success' => true, 'is_favorite' => false];
  //     } else {
  //       $favorite = new Favorite(['shop_id' => $shop->id, 'user_id' => $userId]);
  //       $favorite->save();
  //       return ['success' => true, 'is_favorite' => true];
  //     }
  //   });
  // }

  // 飲食店詳細ページ

  public function detail($shopId)
  {
    $backRoute = url('/');
    $shop = Shop::find($shopId); // Eloquentを使用して店舗情報を取得
    return view('detail', compact('shop', 'backRoute'));
  }
}
