<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Shop;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Booking;
use Illuminate\Support\Facades\Storage;
use App\Models\ShopImage;

class ShopManagerController extends Controller
{
  public function dashboard()
  {
    return view('admin.shop-manager.dashboard');
  }
  // 店舗一覧
  public function index()
  {
    $shops = Shop::where('user_id', auth()->id())->get();
    return view('admin.shop-manager.shops.index', compact('shops'));
  }
  public function createShop()
  {
    $areas = Area::all();
    $genres = Genre::distinct('name')->pluck('id', 'name')->toArray();
    return view('admin.shop-manager.create-shop', compact('areas', 'genres'));
  }


  public function storeShop(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'area_ids' => 'required|array',
      'genre_ids' => 'required|array',
      'outline' => 'required|string',
      'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator)
        ->withInput();
    }

    $shop = Shop::create($validator->validated());
    $shop->areas()->attach($request->input('area_ids'));

    // 既存のジャンルを使用し、新しいジャンルは追加しない
    foreach ($request->input('genre_ids') as $genreId) {
      $existingGenre = Genre::find($genreId);
      if ($existingGenre) {
        $existingGenre->update(['shop_id' => $shop->id]);
      }
    }

    // 画像アップロード処理
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $image) {
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = Storage::putFileAs('public/shop_images', $image, $imageName);

        ShopImage::create([
          'shop_image_url' => 'shop_images/' . $imageName,
          'shop_id' => $shop->id,
        ]);
      }
    }

    return redirect()->route('shop-manager.dashboard')
      ->with('success', '新規店舗を追加しました。');
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

    $shop->update($validatedData);
    $shop->areas()->sync($request->input('area_ids'));

    // 既存のジャンルの shop_id を更新し、新しいジャンルは追加しない
    foreach ($request->input('genre_ids') as $genreId) {
      $existingGenre = Genre::find($genreId);
      if ($existingGenre) {
        $existingGenre->update(['shop_id' => $shop->id]);
      }
    }

    // 画像アップロード処理
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $image) {
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = Storage::putFileAs('public/shop_images', $image, $imageName);

        ShopImage::create([
          'shop_image_url' => 'shop_images/' . $imageName,
          'shop_id' => $shop->id,
        ]);
      }
    }

    return redirect()->route('shop-manager.dashboard')->with('success', '店舗情報を更新しました。');
  }

  public function reservations()
  {
    $bookings = Booking::where('shop_id', auth()->user()->shop_id)->latest()->paginate();
    return view('admin.shop-manager.reservations', compact('bookings'));
  }
}
