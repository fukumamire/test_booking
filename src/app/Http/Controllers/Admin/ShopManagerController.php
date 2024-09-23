<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

  public function createShop()
  {
    $areas = Area::all();
    $genres = Genre::all();
    return view('admin.shop-manager.create-shop', compact('areas', 'genres'));
  }

  public function storeShop(Request $request)
  {
    $validatedData = $request->validate([
      'name' => 'required|string|max:255',
      'area_ids' => 'required|array',
      'genre_ids' => 'required|array',
      'outline' => 'required|string',
      'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $shop = Shop::create($validatedData);
    $shop->areas()->attach($request->input('area_ids'));

    // Genreの処理
    foreach ($request->input('genre_ids') as $genreId) {
      $genre = Genre::find($genreId);
      if ($genre) {
        $genre->shop_id = $shop->id;
        $genre->save();
      }
    }

    // 画像の処理
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $image) {
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = Storage::putFileAs('public/shop_images', $image, $imageName);

        ShopImage::create([
          'shop_image_url' => Storage::url($imagePath),
          'shop_id' => $shop->id,
        ]);
      }
    }

    return redirect()->route('shop-manager.dashboard')->with('success', '新規店舗を追加しました。');
  }

  public function editShop(Shop $shop)
  {
    $areas = Area::all();
    $genres = Genre::all();
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

    // Genreの処理
    $shop->genres()->each(function ($genre) use ($shop) {
      $genre->shop_id = $shop->id;
      $genre->save();
    });

    // 画像の処理
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $image) {
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = Storage::putFileAs('public/shop_images', $image, $imageName);

        ShopImage::create([
          'shop_image_url' => Storage::url($imagePath),
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
