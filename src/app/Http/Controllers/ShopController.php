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

    // エリアでのフィルタリング
    if ($request->has('area') && $request->area != '') {
      $query->whereHas('areas', function ($q) use ($request) {
        $q->where('id', $request->area);
      });
    }

    // ジャンルでのフィルタリング
    if ($request->has('genre') && $request->genre != '') {
      $query->whereHas('genres', function ($q) use ($request) {
        $q->where('name', $request->genre);
      });
    }

    // 検索ワードでのフィルタリング
    if ($request->has('word')) {
      $query->where('name', 'like', '%' . $request->word . '%');
    }

    $shops = $query->paginate(10); // ページネーションを適用

    // エリアとジャンルのリストを取得
    $areas = Area::all();
    $genres = Genre::select('name')->distinct()->get();

    return view('index', compact('shops', 'areas', 'genres'));
  }
}
