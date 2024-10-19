<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


use App\Models\User;
use App\Models\Shop;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Booking;
use Illuminate\Support\Facades\Storage;
use App\Models\ShopImage;
use Spatie\Permission\Models\Role;


class ShopManagerController extends Controller
{
  public function __construct()
  {
    $this->middleware(function ($request, $next) {
      if (!auth()->check()) {
        return redirect()->route('shop-manager.login');
      }

      $user = auth()->user();
      if (!$user instanceof User || !$user->hasRole('shop-manager')) {
        return redirect()->route('shop-manager.login')->with('error', 'Invalid shop manager');
      }

      return $next($request);
    });
  }

  public function dashboard()
  {
    return view('admin.shop-manager.dashboard');
  }

  public function index()
  {
    // 現在のユーザーを取得
    $user = auth()->user();

    // ユーザーに関連する店舗を取得（エリアとジャンルも一緒に取得）
    $shops = Shop::with(['areas', 'genres'])
      ->where('user_id', $user->id)
      ->get();

    // ビューに渡す
    return view('admin.shop-manager.shops.index', compact('shops'));
  }

  public function createShop()
  {
    // エリアとジャンルを取得
    $areas = Area::all();
    $genres = Genre::distinct('name')->pluck('id', 'name')->toArray();
    return view('admin.shop-manager.create-shop', compact('areas', 'genres'));
  }

  public function storeShop(Request $request)
  {
    Log::info('StoreShop method reached.');
    // バリデーション
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'area_ids' => 'required|array',
      'genres' => 'required|string',
      'outline' => 'required|string',
      'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator)
        ->withInput();
    }

    $shop = Shop::create($validator->validated());

    // エリアの登録
    $shop->areas()->attach($request->input('area_ids'));

    // ジャンルの処理
    $genreNames = explode(',', $request->input('genres'));
    foreach ($genreNames as $genreName) {
      $genreName = trim($genreName);
      if (!empty($genreName)) {
        Genre::firstOrCreate(['name' => $genreName, 'shop_id' => $shop->id]);
      }
    }

    // 画像アップロード処理
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $image) {
        try {
          // 元のファイル名と拡張子を取得
          $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
          $extension = $image->getClientOriginalExtension();
          $encodedName = urlencode($originalName);

          // ユニークなファイル名を生成
          $imageName = uniqid() . '_' . $encodedName . '.' . $extension;

          // 画像を 'public/shop_images' に保存
          $imagePath = $image->storeAs('public/shop_images', $imageName);

          // URLの生成（'storage/shop_images/...' となる）
          $imageUrl = Storage::url($imagePath);

          // ShopImageモデルに保存
          ShopImage::create([
            'shop_image_url' => $imageUrl,
            'shop_id' => $shop->id,
          ]);

          // 成功した場合のログ出力
          Log::info('Image uploaded successfully: ' . $imageName);
        } catch (\Exception $e) {
          // エラーが発生した場合の処理
          Log::error('Image upload error for file ' . $image->getClientOriginalName() . ': ' . $e->getMessage());
          return redirect()->back()->with('error', '画像のアップロードに失敗しました: ' . $e->getMessage());
        }
      }
    }


    // ログイン中のユーザーと新規店舗を結びつける
    $user = auth()->user();
    if ($user instanceof User && $user->hasRole('shop-manager')) {
      // 新規店舗のuser_idを更新
      $shop->update(['user_id' => $user->id]);

      // ユーザーのshop_idも更新
      $user->update(['shop_id' => $shop->id]);
    } else {
      return redirect()->back()->with('error', '店舗管理者としてログインしてください。');
    }

    return redirect()->route('shop-manager.shops.index')->with('success', '新規店舗を追加しました。');

  }

  public function editShop(Shop $shop)
  {
    $areas = Area::all();
    $genres = Genre::select('id', 'name')->distinct()->get(); // 重複を排除
    return view('admin.shop-manager.edit-shop', compact('shop', 'areas', 'genres'));
  }


  public function updateShop(Request $request, Shop $shop)
  {
    $validatedData = $request->validate([
      'name' => 'required|string|max:255',
      'area_ids' => 'required|array',
      'genre_ids' => 'required|array',
      'outline' => 'required|string',
      'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    // 既存のジャンルの shop_id を更新し、新しいジャンルは追加しない
    foreach ($request->input('genre_ids') as $genreId) {
      $existingGenre = Genre::find($genreId);
      if ($existingGenre && $shop->genres()->where('id', $genreId)->exists()) {
        $existingGenre->update(['shop_id' => $shop->id]);
      }
    }

    // 画像アップロード処理
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $image) {
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = Storage::putFileAs('public/shop_images', $image, $imageName);

        ShopImage::create([
          'shop_image_url' => $imageName,
          'shop_id' => $shop->id,
        ]);
      }
    }

    // 店舗の情報を更新
    $shop->update([
      'name' => $validatedData['name'],
      'outline' => $validatedData['outline'],
      'area_ids' => $validatedData['area_ids']
    ]);

    // エリア関連付けの更新
    $shop->areas()->sync($validatedData['area_ids']);

    return redirect()->route('shop-manager.dashboard')->with('success', '店舗情報を更新しました。');
  }
  //店舗削除
  public function destroy(Shop $shop)
  {
    if (!auth()->check()) {
      return redirect()->route('shop-manager.login');
    }

    $user = auth()->user();

    if (!$user instanceof User || !$user->hasRole('shop-manager')) {
      abort(403, 'Invalid shop manager');
    }

    if ($shop->user_id !== $user->id) {
      abort(403, 'You do not have permission to delete this shop');
    }

    // 関連データの論理削除
    $shop->areas()->detach();
    $shop->genres()->delete();
    $shop->images()->delete();
    $shop->bookings()->delete();

    // 店舗の論理削除
    $shop->delete();

    return redirect()->route('shop-manager.shops.index')->with('success', '店舗削除をしました。～Shop deleted successfully～');
  }

  public function restore($id)
  {
    $shop = Shop::withTrashed()->findOrFail($id);

    if (!auth()->check()) {
      return redirect()->route('shop-manager.login');
    }

    $user = auth()->user();

    if (!$user instanceof User || !$user->hasRole('shop-manager')) {
      abort(403, 'Invalid shop manager');
    }

    if ($shop->user_id !== $user->id) {
      abort(403, 'You do not have permission to restore this shop');
    }

    // ショップの復元
    $shop->restore();

    // 関連データの復元
    $shop->genres()->restore();
    $shop->images()->restore();
    $shop->bookings()->restore();

    // エリアの関連付けを復元
    $shop->areas()->sync($shop->areas()->pluck('id')->toArray());

    // ユーザーとショップの関連付けを復元
    $user->update(['shop_id' => $shop->id]);

    return redirect()->route('shop-manager.shops.index')->with('success', 'Shop restored successfully');
  }


  public function reservations()
  {
    $user = auth()->user();

    if (!$user instanceof User || !$user->hasRole('shop-manager')) {
      abort(403, 'Invalid shop manager');
    }

    if (!$user->shop_id) {
      abort(404, 'Associated shop not found');
    }

    $bookings = Booking::where('shop_id', $user->shop_id)->latest()->paginate();
    return view('admin.shop-manager.reservations', compact('bookings'));
  }
}
