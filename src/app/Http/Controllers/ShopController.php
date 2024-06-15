<?php

namespace App\Http\Controllers;

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
    if ($request->has('keyword')) {
      $query->where(function ($query) use ($request) {
        $query->where('name', 'like', '%' . $request->keyword . '%')
          ->orWhere('outline', 'like', '%' . $request->keyword . '%');
      });
    }

    // エリアでのフィルタリング
    if ($request->has('area')) {
      $query->whereHas('areas', function ($q) use ($request) {
        $q->where('id', $request->area);
      });
    }

    // ジャンルでのフィルタリング
    if ($request->has('genre')) {
      $query->whereHas('genres', function ($q) use ($request) {
        $q->where('name', $request->genre);
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
    $shops = Shop::paginate(20); // ページネーションを使用してデータを取得
    $areas = Area::all();
    $genres = Genre::select('name')->distinct()->get(); // ジャンルの名前を選択し、重複を除去して取得
    return view('index', compact('shops', 'areas', 'genres')); // 'index'ビューに渡すデータを準備
  }
}
