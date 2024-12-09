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


  // public function import(AdminShopImportRequest $request)
  // {
  //   try {
  //     $file = $request->file('file');
  //     Log::info('File uploaded: ' . $file->getClientOriginalName());

  //     $import = new ShopImport();
  //     $results = Excel::toArray($import, $file->getRealPath(), null, \Maatwebsite\Excel\Excel::CSV);

  //     $this->processImportResults($results);

  //     Log::info('Shop imported successfully.');
  //     return redirect()->back()->with('success', 'Shops imported successfully.');
  //   } catch (\Exception $e) {
  //     Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
  //     return redirect()->back()->withErrors(['インポート中にエラーが発生しました。']);
  //   }
  // }
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

  //不要のため一時コメントアウト
  // private function processImportResults($results)
  // {
  //   foreach ($results as $result) {
  //     if (empty($result)) {
  //       Log::warning("空の行が検出されました。");
  //       continue; // 空の行をスキップ
  //     }

  //     Log::info("処理中のデータ: " . json_encode($result));

  //     try {
  //       DB::transaction(function () use ($result) {
  //         // 店舗のupsert
  //         $shop = Shop::updateOrCreate(
  //           ['id' => $result['id'] ?? null],
  //           [
  //             'name' => $result['name'] ?? '未設定',
  //             'outline' => $result['outline'] ?? '',
  //             'user_id' => $result['user_id'] ?? 1,
  //             'updated_at' => $result['updated_at'] ?? now()
  //           ]
  //         );

  //         Log::info("作成/更新された店舗: " . json_encode($shop->toArray()));

  //         if (empty($shop->name)) {
  //           Log::warning("店舗名が設定されていません。ID: " . ($shop->id ?? 'なし'));
  //         }

  //         // 各情報を処理
  //         $this->updateAreaInfo($shop, $result['area_name'] ?? '');
  //         $this->updateGenres($shop, $result['genres'] ?? []);
  //         $this->updateShopImage($shop, $result['image_url'] ?? '');
  //       });
  //     } catch (\Exception $e) {
  //       Log::error("インポート処理中にエラーが発生しました: " . $e->getMessage());
  //       continue; // エラーが発生しても次の行を処理
  //     }
  //   }
  // }


  // private function updateAreaInfo(Shop $shop, string $areaName)
  // {
  //   $definedAreas = static::$DEFINED_AREAS;

  //   if (!isset($definedAreas[$areaName])) {
  //     Log::warning("無効な地域名が指定されました: '$areaName'. 許可された値は「東京」「大阪」「福岡」または「東京都」「大阪府」「福岡県」のみです。");
  //     return; // 無効な地域名の場合は処理をスキップ
  //   }

  //   Log::debug("エリア名 (標準化後): " . $definedAreas[$areaName]);

  //   $area = Area::where('name', $definedAreas[$areaName])->first();
  //   if (!$area) {
  //     Log::warning("地域が見つかりません: '" . $definedAreas[$areaName] . "'");
  //     return; // 存在しない地域の場合は処理をスキップ
  //   }

  //   DB::table('shop_areas')->updateOrInsert(
  //     ['shop_id' => $shop->id, 'area_id' => $area->id],
  //     ['updated_at' => now()]
  //   );
  // }

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

  // private function updateGenres($shop, array $genres)
  // {
  //   foreach ($genres as $genreName) {
  //     $standardizedGenreName = trim($genreName);

  //     // 既存のジャンルを取得
  //     $genre = Genre::where('name', $standardizedGenreName)->first();

  //     if (!$genre) {
  //       Log::warning("未登録のジャンル名が指定されました: '$genreName'");
  //       continue; // 未登録のジャンルの場合は次のアイテムに進む
  //     }

  //     // genres テーブルに関連付けを更新
  //     DB::table('genres')->updateOrInsert(
  //       ['id' => $genre->id],
  //       ['shop_id' => $shop->id, 'updated_at' => now()]
  //     );
  //   }
  // }

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
