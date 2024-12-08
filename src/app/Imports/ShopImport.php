<?php

namespace App\Imports;

use App\Models\Shop;
use App\Models\Area;
use App\Models\Genre;
use App\Models\ShopImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsErrors as SkipsErrorsTrait;

class ShopImport implements ToModel, WithBatchInserts, WithChunkReading
{
  const DEFINED_AREAS = [
    '東京' => '東京都',
    '東京都' => '東京都',
    '大阪' => '大阪府',
    '大阪府' => '大阪府',
    '福岡' => '福岡県',
    '福岡県' => '福岡県',
  ];

  const DEFINED_GENRES = [
    '寿司' => '寿司',
    '焼肉' => '焼肉',
    'イタリアン' => 'イタリアン',
    '居酒屋' => '居酒屋',
    'ラーメン' => 'ラーメン',
  ];

  protected $columnMappings = [
    'Shop' => [
      'name' => '店舗名',
      'user_id' => 'ユーザーID',
      'outline' => '店舗概要',
    ],
    'Area' => [
      'name' => '地域',
    ],
    'Genre' => [
      'name' => 'ジャンル',
    ],
    'ShopImage' => [
      'shop_image_url' => '画像URL',
    ],
  ];


  // private function cleanData(array $data)
  // {
  //   return array_map(
  //     function ($key, $value) {
  //       // id フィールドはそのまま返す
  //       if ($key === 'id') {
  //         return $value;
  //       }

  //       // UTF-8に変換
  //       $value = mb_convert_encoding($value, 'UTF-8', 'auto');

  //       // URLの場合はそのまま返す
  //       if (preg_match('/^https?:\/\//', $value)) {
  //         return $value;
  //       }

  //       // 空白をトリム
  //       $value = is_string($value) ? trim(mb_convert_kana($value, 'as')) : $value;

  //       // 不正な文字を除去（空白を保持）
  //       $value = preg_replace('/[^\p{Han}\p{Hiragana}\p{Katakana}\d\s]+/u', '', $value);

  //       return is_string($value) ? trim(mb_convert_kana($value, 'as')) : $value;
  //     },
  //     array_keys($data),
  //     $data
  //   );
  // }

  private function cleanData(array $data)
  {
    // id 列を削除（主キーはデータベースで自動生成）
    if (isset($data['id'])) {
      unset($data['id']);
    }

    return array_map(function ($key, $value) {
      // id フィールドはすでに削除済みなので、このチェックは不要

      // UTF-8に変換
      $value = mb_convert_encoding($value, 'UTF-8', 'auto');

      // URLの場合はそのまま返す
      if (preg_match('/^https?:\/\//', $value)) {
        return $value;
      }

      // 空白をトリム
      $value = is_string($value) ? trim(mb_convert_kana($value, 'as')) : $value;

      // 不正な文字を除去（空白を保持）
      $value = preg_replace('/[^\p{Han}\p{Hiragana}\p{Katakana}\d\s]+/u', '', $value);

      return is_string($value) ? trim(mb_convert_kana($value, 'as')) : $value;
    }, array_keys($data), $data);
  }

  // public function model(array $row): ?Shop
  // {
  //   // ヘッダー行をスキップ
  //   static $headerProcessed = false;
  //   if (!$headerProcessed) {
  //     $headerProcessed = true;
  //     return null;
  //   }

  //   // 既存のIDがある場合はスキップ
  //   if (
  //     isset($row['id']) && Shop::where('id', $row['id'])->exists()
  //   ) {
  //     Log::info("ID重複: {$row['id']} をスキップしました。");
  //     return null;
  //   }

  //   $cleanedRow = $this->cleanData($row);

  //   // 既存の店舗を検索
  //   $existingShop = Shop::where('name', $cleanedRow[0])->first();

  //   if ($existingShop) {
  //     // 既存の店舗がある場合は更新
  //     return $this->updateExistingShop($existingShop, $cleanedRow);
  //   }

  //   // 新しい店舗として保存
  //   return $this->createNewShop($cleanedRow);
  // }

  // private function updateExistingShop(Shop $shop, array $cleanedRow)
  // {
  //   return DB::transaction(function () use ($shop, $cleanedRow) {
  //     try {
  //       $shop->update([
  //         'outline' => $cleanedRow[4],
  //         'user_id' => !empty(trim($cleanedRow[1])) ? filter_var($cleanedRow[1], FILTER_VALIDATE_INT) : 1,
  //         'updated_at' => now(),
  //       ]);

  //       // エリア情報の更新
  //       $this->updateAreaInfo($shop, $cleanedRow[2]);

  //       // ジャンル情報の更新
  //       $this->updateGenres($shop, $cleanedRow[3]);

  //       // 画像情報の更新
  //       $this->updateShopImage($shop, $cleanedRow[5]);

  //       return $shop;
  //     } catch (\Exception $e) {
  //       Log::error("既存の店舗の更新中にエラーが発生しました: " . $e->getMessage());
  //       Log::error("エラーが発生したデータ: " . json_encode($cleanedRow));
  //       throw $e; // エラーを再スローしてトランザクションをロールバック
  //     }
  //   });
  // }

  // private function createNewShop(array $cleanedRow)
  // {
  //   return DB::transaction(function () use ($cleanedRow) {
  //     try {
  //       $shop = Shop::create([
  //         'name' => $cleanedRow[0],
  //         'outline' => $cleanedRow[4],
  //         'user_id' => !empty(trim($cleanedRow[1])) ? filter_var($cleanedRow[1], FILTER_VALIDATE_INT) : 1,
  //       ]);

  //       // エリア情報の登録
  //       $this->updateAreaInfo($shop, $cleanedRow[2]);

  //       // ジャンル情報の登録
  //       $this->updateGenres($shop, $cleanedRow[3]);

  //       // 画像情報の登録
  //       $this->updateShopImage($shop, $cleanedRow[5]);

  //       return $shop;
  //     } catch (\Exception $e) {
  //       Log::error("新しい店舗の作成中にエラーが発生しました: " . $e->getMessage());
  //       Log::error("エラーが発生したデータ: " . json_encode($cleanedRow));
  //       throw $e; // エラーを再スローしてトランザクションをロールバック
  //     }
  //   });
  // }

  public function model(array $row): ?Shop
  {
    // ヘッダー行をスキップ
    static $headerProcessed = false;
    if (!$headerProcessed) {
      $headerProcessed = true;
      return null;
    }

    // 既存のIDがある場合はスキップ
    if (
      isset($row['id']) && Shop::where('id', $row['id'])->exists()
    ) {
      Log::info("ID重複: {$row['id']} をスキップしました。");
      return null;
    }

    $cleanedRow = $this->cleanData($row);

    // 既存の店舗を検索
    $existingShop = Shop::where('name', $cleanedRow[0])->first();

    if ($existingShop) {
      // 既存の店舗がある場合は更新
      return $this->updateExistingShop($existingShop, $cleanedRow);
    }

    // 新しい店舗として保存
    return $this->createNewShop($cleanedRow);
  }

  private function createNewShop(array $cleanedRow)
  {
    return DB::transaction(function () use ($cleanedRow) {
      try {
        $shop = Shop::create([
          'name' => $cleanedRow[0],
          'outline' => $cleanedRow[4],
          'user_id' => !empty(trim($cleanedRow[1])) ? filter_var($cleanedRow[1], FILTER_VALIDATE_INT) : 1,
        ]);

        // エリア情報の登録
        $this->updateAreaInfo($shop, $cleanedRow[2]);

        // ジャンル情報の登録
        $this->updateGenres($shop, $cleanedRow[3]);

        // 画像情報の登録
        $this->updateShopImage($shop, $cleanedRow[5]);

        return $shop;
      } catch (\Exception $e) {
        Log::error("新しい店舗の作成中にエラーが発生しました: " . $e->getMessage());
        Log::error("エラーが発生したデータ: " . json_encode($cleanedRow));
        throw $e; // エラーを再スローしてトランザクションをロールバック
      }
    });
  }

  private function updateExistingShop(Shop $shop, array $cleanedRow)
  {
    return DB::transaction(function () use ($shop, $cleanedRow) {
      try {
        $shop->update([
          'outline' => $cleanedRow[4],
          'user_id' => !empty(trim($cleanedRow[1])) ? filter_var($cleanedRow[1], FILTER_VALIDATE_INT) : 1,
          'updated_at' => now(),
        ]);

        // エリア情報の更新
        $this->updateAreaInfo($shop, $cleanedRow[2]);

        // ジャンル情報の更新
        $this->updateGenres($shop, $cleanedRow[3]);

        // 画像情報の更新
        $this->updateShopImage($shop, $cleanedRow[5]);

        return $shop;
      } catch (\Exception $e) {
        Log::error("既存の店舗の更新中にエラーが発生しました: " . $e->getMessage());
        Log::error("エラーが発生したデータ: " . json_encode($cleanedRow));
        throw $e; // エラーを再スローしてトランザクションをロールバック
      }
    });
  }

  private function updateAreaInfo(Shop $shop, string $areaName)
  {
    $standardizedAreaName = self::DEFINED_AREAS[$areaName] ?? null;

    if ($standardizedAreaName === null) {
      Log::warning("無効な地域名が指定されました: '$areaName'. 許可された値は「東京」「大阪」「福岡」または「東京都」「大阪府」「福岡県」のみです。");
      return; // 無効な地域名の場合は処理をスキップ
    }

    Log::debug("エリア名 (標準化後): " . $standardizedAreaName);

    $area = Area::where('name', $standardizedAreaName)->first();
    if (!$area) {
      Log::warning("地域が見つかりません: '$standardizedAreaName'");
      return; // 存在しない地域の場合は処理をスキップ
    }

    DB::table('shop_areas')->updateOrInsert(
      ['shop_id' => $shop->id, 'area_id' => $area->id],
      ['updated_at' => now()]
    );
  }


  private function updateGenres(Shop $shop, string $genresString)
  {
    $genres = explode(',', $genresString);

    foreach ($genres as $genreName) {
      $standardizedGenreName = trim($genreName);

      // ジャンのバリデーション
      if (!array_key_exists($standardizedGenreName, self::DEFINED_GENRES)) {
        Log::warning("無効なジャンル名が指定されました: '$genreName'. 許可された値は「寿司」「焼肉」「イタリアン」「居酒屋」「ラーメン」のみです。");
        continue; // 無効なジャンルの場合は次のアイテムに進む
      }

      // ジャンルが存在するか確認
      $genre = Genre::where('name', $standardizedGenreName)->first();

      if (!$genre) {
        // ジャンルが存在しない場合は新規作成
        $genre = Genre::create([
          'name' => $standardizedGenreName,
          'created_at' => now(),
          'updated_at' => now(),
        ]);
      }

      // genres テーブルに関連付けを更新
      DB::table('genres')->updateOrInsert(
        ['id' => $genre->id],
        ['shop_id' => $shop->id, 'updated_at' => now()]
      );
    }
  }

  private function updateShopImage(Shop $shop, string $imageUrl)
  {
    if (!empty($imageUrl)) {
      ShopImage::updateOrCreate([
        'shop_id' => $shop->id,
      ], [
        'shop_image_url' => $imageUrl,
        'updated_at' => now(),
      ]);
    }
  }

  public function batchSize(): int
  {
    return 10; // 一度にインポートするバッチサイズ 小さな値（例：10）に設定
  }

  public function chunkSize(): int
  {
    return 500; // チャンクサイズ
  }


  public function onError(\Throwable $e)
  {
    Log::error('Error importing shops: ' . $e->getMessage(), [
      'exception' => $e,
      'trace' => $e->getTraceAsString()
    ]);
    session()->push('import_errors', 'インポート中にエラーが発生しました。データの中身を確認し再度、データを送信してください。');
  }
}
