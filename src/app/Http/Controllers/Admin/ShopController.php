<?php

namespace App\Http\Controllers\Admin;

use App\Imports\ShopImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\Shop;
use App\Models\Area;
use App\Models\Genre;
use App\Models\ShopImage;
use App\Http\Requests\AdminShopImportRequest;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
  public static $DEFINED_AREAS = [
    '東京' => '東京都',
    '東京都' => '東京都',
    '大阪' => '大阪府',
    '大阪府' => '大阪府',
    '福岡' => '福岡県',
    '福岡県' => '福岡県',
  ];

  public static $DEFINED_GENRES = [
    '寿司' => '寿司',
    '焼肉' => '焼肉',
    'イタリアン' => 'イタリアン',
    '居酒屋' => '居酒屋',
    'ラーメン' => 'ラーメン',
  ];

  public function __construct()
  {
    $this->middleware('auth:admin');
  }


  public function import(AdminShopImportRequest $request)
  {
    $file = $request->file('csv_file');

    // CSVデータを配列形式で取得
    $csvData = array_map('str_getcsv', file($file->getRealPath()));

    // 日本語ヘッダーと内部キーのマッピング
    $headerMapping = [
      '店舗名' => 'name',
      'ユーザーID' => 'user_id',
      '地域' => 'area_name',
      'ジャンル' => 'genres',
      '画像URL' => 'image_url',
    ];

    // ヘッダーを取得してマッピング
    $header = array_map(function ($col) use ($headerMapping) {
      return $headerMapping[$col] ?? null;
    }, array_shift($csvData));

    // ヘッダー検証
    if (in_array(null, $header, true) || count($header) !== count($headerMapping)) {
      abort(422, "CSVファイルのヘッダーが不正です: " . implode(', ', $header));
    }

    // データの検証と登録
    foreach ($csvData as $lineNumber => $row) {
      if (count($header) !== count($row)) {
        abort(422, "CSVの" . ($lineNumber + 2) . "行目に列数の不一致があります。");
      }

      $data = array_combine($header, $row);

      $this->validateRow($data, $lineNumber + 2);

      $shop = Shop::create([
        'name' => $data['name'],
        'user_id' => $data['user_id'] ?? 1,
        'area_name' => $data['area_name'],
        'genres' => $data['genres'],
        'image_url' => $data['image_url'],
      ]);

      // エリア情報の更新
      $this->updateAreaInfo($shop, $data['area_name']);

      // ジャンルの更新
      $this->updateGenres($shop, $data['genres']);

      // 画像の更新
      $this->updateShopImage($shop, $data['image_url']);
    }

    return redirect()->back()->with('success', 'CSVのインポートが完了しました！');
  }

  
  private function updateAreaInfo(Shop $shop, string $areaName)
  {
    if (!isset(static::$DEFINED_AREAS[$areaName])) {
      Log::warning("無効な地域名が指定されました: '$areaName'. 許可された値は「東京」「大阪」「福岡」または「東京都」「大阪府」「福岡県」のみです。");
      return; // 無効な地域名の場合は処理をスキップ
    }

    Log::debug("エリア名 (標準化後): " . static::$DEFINED_AREAS[$areaName]);

    $area = Area::where('name', static::$DEFINED_AREAS[$areaName])->first();
    if (!$area) {
      Log::warning("地域が見つかりません: '" . static::$DEFINED_AREAS[$areaName] . "'");
      return; // 存在しない地域の場合は処理をスキップ
    }

    DB::table('shop_areas')->updateOrInsert(
      ['shop_id' => $shop->id, 'area_id' => $area->id],
      ['updated_at' => now()]
    );
  }

  private function updateGenres($shop, array $genres)
  {
    foreach ($genres as $genreName) {
      $standardizedGenreName = trim($genreName);

      if (!isset(static::$DEFINED_GENRES[$standardizedGenreName])) {
        Log::warning("無効なジャンル名が指定されました: '$genreName'. 許可された値は「寿司」「焼肉」「イタリアン」「居酒屋」「ラーメン」のみです。");
        continue; // 無効なジャンルの場合は次のアイテムに進む
      }

      $genre = Genre::where('name', static::$DEFINED_GENRES[$standardizedGenreName])->first();

      if (!$genre) {
        Log::warning("未登録のジャンル名が指定されました: '$genreName'");
        continue; // 未登録のジャンルの場合は次のアイテムに進む
      }

      DB::table('genres')->updateOrInsert(
        ['id' => $genre->id],
        ['shop_id' => $shop->id, 'updated_at' => now()]
      );
    }
  }

  private function updateShopImage(Shop $shop, string $imageUrl)
  {
    if (!empty($imageUrl)) {
      $shop->shopImages()->updateOrCreate([], [
        'shop_image_url' => $imageUrl,
        'updated_at' => now(),
      ]);
    }
  }


  public function importForm()
  {
    return view('admin.shops.import');
  }
}
