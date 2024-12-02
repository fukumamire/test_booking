<?php

namespace App\Imports;

use App\Models\Shop;
use App\Models\Area;
use App\Models\Genre;
use App\Models\ShopImage;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Facades\Log;


class ShopImport implements ToModel, WithBatchInserts, WithChunkReading
{
  const DEFINED_AREAS = [
    '東京' => '東京都',
    '大阪' => '大阪府',
    '福岡' => '福岡県',
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
      'user_id' => 'ユーザーＩＤ',
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

  // private function cleanData($data)
  // {
  //   return array_map(function ($value) {
  //     // ANSI形式の文字列をUTF-8に変換
  //     $value = iconv('Shift_JIS', 'UTF-8//IGNORE', $value);

  //     // 不正な文字を除去（空白を保持）
  //     $value = preg_replace('/[^\p{Han}\p{Hiragana}\p{Katakana}\d\s]+/u', '', $value); // 漢字・ひらがな・カタカナ・数字・空白以外を削除

  //     return is_string($value) ? trim(mb_convert_kana($value, 'as')) : $value;
  //   }, $data);
  // }

  // private function cleanData($data)
  // {
  //   return array_map(function ($value) {
  //     // UTF-8に変換する前に、不完全なマルチバイト文字を削除
  //     $value = preg_replace('/[\x80-\xFF]/', '', $value);

  //     // UTF-8に変換
  //     $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);

  //     // 不正な文字を除去（空白を保持）
  //     $value = preg_replace('/[^\p{Han}\p{Hiragana}\p{Katakana}\d\s]+/u', '', $value);

  //     return is_string($value) ? trim(mb_convert_kana($value, 'as')) : $value;
  //   }, $data);
  // }

  private function cleanData($data)
  {
    return array_map(function ($value) {
      // UTF-8に変換
      $value = mb_convert_encoding($value, 'UTF-8', 'auto');

      // 不正な文字を除去（空白を保持）
      $value = preg_replace('/[^\p{Han}\p{Hiragana}\p{Katakana}\d\s]+/u', '', $value);

      return is_string($value) ? trim(mb_convert_kana($value, 'as')) : $value;
    }, $data);
  }



  // public function model(array $row): ?Shop
  // {
  //   $cleanedRow = $this->cleanData($row);

  //   Log::info('Received row data:', $row);
  //   Log::info('Cleaned row data:', $cleanedRow);

  //   if (empty(trim($cleanedRow[0]))) {
  //     throw new \Exception("店舗名が空です。");
  //   }

  //   return DB::transaction(function () use ($cleanedRow) {
  //     try {
  //       // ユーザーIDの整形
  //       $userId = !empty(trim($cleanedRow[1])) ? filter_var($cleanedRow[1], FILTER_VALIDATE_INT) : null;

  //       // 既存の店舗を検索、なければ新規作成
  //       $existingShop = Shop::where('name', $cleanedRow[0])->first();

  //       if ($existingShop) {
  //         // 既存の店舗がある場合、更新
  //         $existingShop->update([
  //           'outline' => $cleanedRow[4],
  //           'user_id' => $cleanedRow[1] ?? null,
  //         ]);
  //         $shop = $existingShop;
  //       } else {
  //         // 新しい店舗を作成
  //         $shop = Shop::create([
  //           'name' => $cleanedRow[0],
  //           'outline' => $cleanedRow[4],
  //           'user_id' =>
  //           $cleanedRow[1] ?? null,
  //         ]);
  //       }

  //       // エリア情報のインポート
  //       $areaName = trim($cleanedRow[2]);
  //       Log::debug("エリア名 (未処理): " . $areaName);

  //       // DEFINED_AREAS 配列を逆順にして、短縮形から完全名へのマッピングを作成
  //       $reverseDefinedAreas = array_flip(self::DEFINED_AREAS);

  //       // 入力されたエリア名が短縮形か完全名かのいずれかで標準化
  //       $standardizedAreaName = $reverseDefinedAreas[$areaName] ?? self::DEFINED_AREAS[$areaName] ?? null;
  //       Log::debug("標準化されたエリア名: " . $standardizedAreaName);

  //       // 標準化されたエリア名が有効か確認
  //       if (!$standardizedAreaName || !in_array($standardizedAreaName, ['東京都', '大阪府', '福岡県'])) {
  //         throw new \Exception("地域が不正です。入力された値: '$areaName'。許可された値は「東京」「大阪」「福岡」または「東京都」「大阪府」「福岡県」のみです。");
  //       }

  //       $area = Area::where('name', $standardizedAreaName)->first();

  //       if (!$area) {
  //         throw new \Exception("地域が見つかりません。許可された値は「東京」「大阪」「福岡」または「東京都」「大阪府」「福岡県」のみです。");
  //       }

  //       DB::table('shop_areas')->updateOrInsert(
  //         ['shop_id' => $shop->id, 'area_id' => $area->id],
  //         ['updated_at' => now()]
  //       );

  //       // ジャンル情報のインポート
  //       $genres = explode(',', $cleanedRow[3]);

  //       foreach ($genres as $genreName) {
  //         $standardizedGenreName = trim($genreName);

  //         // ジャンルのバリデーション
  //         if (!array_key_exists($standardizedGenreName, self::DEFINED_GENRES)) {
  //           throw new \Exception("ジャンルが不正です。許可された値は「寿司」「焼肉」「イタリアン」「居酒屋」「ラーメン」のみです。");
  //         }

  //         // ジャンルが存在するか確認
  //         $genre = Genre::where('name', $standardizedGenreName)->first();

  //         if (!$genre) {
  //           // ジャンルが存在しない場合は新規作成
  //           $genre = Genre::create([
  //             'name' => $standardizedGenreName,
  //             'created_at' => now(),
  //             'updated_at' => now(),
  //           ]);
  //         }

  //         // genres テーブルに関連付けを追加
  //         DB::table('genres')->updateOrInsert(
  //           ['id' => $genre->id],
  //           ['shop_id' => $shop->id, 'updated_at' => now()]
  //         );
  //       }

  //       // 画像情報のインポート
  //       if (!empty($cleanedRow[5])) {
  //         ShopImage::create([
  //           'shop_id' => $shop->id,
  //           'shop_image_url' => $cleanedRow[5],
  //           'created_at' => now(),
  //           'updated_at' => now(),
  //         ]);
  //       }

  //       return $shop; // Shopモデルを返す
  //     } catch (\Exception $e) {
  //       Log::error("店舗のインポート中にエラーが発生しました: " . $e->getMessage());
  //       throw $e; // エラーを再スローしてトランザクションをロールバック
  //     }
  //   });
  // }

  public function model(array $row): ?Shop
  {
    $cleanedRow = $this->cleanData($row);

    Log::info('Received row data:', $row);
    Log::info('Cleaned row data:', $cleanedRow);

    if (empty(trim($cleanedRow[0]))) {
      Log::warning("店舗名が空です。データ: " . json_encode($cleanedRow));
      return null;
    }

    return DB::transaction(function () use ($cleanedRow) {
      try {
        // ユーザーIDの整形
        $userId = !empty(trim($cleanedRow[1])) ? filter_var($cleanedRow[1], FILTER_VALIDATE_INT) : null;

        // 既存の店舗を検索、なければ新規作成
        $existingShop = Shop::where('name', $cleanedRow[0])->first();

        if ($existingShop) {
          // 既存の店舗がある場合、更新
          $existingShop->update([
            'outline' => $cleanedRow[4],
            'user_id' => $userId,
          ]);
          $shop = $existingShop;
        } else {
          // 新しい店舗を作成
          $shop = Shop::create([
            'name' => $cleanedRow[0],
            'outline' => $cleanedRow[4],
            'user_id' => $userId,
          ]);
        }

        // エリア情報のインポート
        $areaName = trim($cleanedRow[2]);
        Log::debug("エリア名 (未処理): " . $areaName);

        // DEFINED_AREAS 配列を逆順にして、短縮形から完全名へのマッピングを作成
        $reverseDefinedAreas = array_flip(self::DEFINED_AREAS);

        // 入力されたエリア名が短縮形か完全名かのいずれかで標準化
        $standardizedAreaName = $reverseDefinedAreas[$areaName] ?? self::DEFINED_AREAS[$areaName] ?? null;
        Log::debug("標準化されたエリア名: " . $standardizedAreaName);

        // 標準化されたエリア名が有効か確認
        if (!$standardizedAreaName || !in_array($standardizedAreaName, ['東京都', '大阪府', '福岡県'])) {
          throw new \Exception("地域が不正です。入力された値: '$areaName'。許可された値は「東京」「大阪」「福岡」または「東京都」「大阪府」「福岡県」のみです。");
        }

        $area = Area::where('name', $standardizedAreaName)->first();

        if (!$area) {
          throw new \Exception("地域が見つかりません。許可された値は「東京」「大阪」「福岡」または「東京都」「大阪府」「福岡県」のみです。");
        }

        DB::table('shop_areas')->updateOrInsert(
          ['shop_id' => $shop->id, 'area_id' => $area->id],
          ['updated_at' => now()]
        );

        // ジャンル情報のインポート
        $genres = explode(',', $cleanedRow[3]);

        foreach ($genres as $genreName) {
          $standardizedGenreName = trim($genreName);

          // ジャンルのバリデーション
          if (!array_key_exists($standardizedGenreName, self::DEFINED_GENRES)) {
            throw new \Exception("ジャンルが不正です。許可された値は「寿司」「焼肉」「イタリアン」「居酒屋」「ラーメン」のみです。");
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

          // genres テーブルに関連付けを追加
          DB::table('genres')->updateOrInsert(
            ['id' => $genre->id],
            ['shop_id' => $shop->id, 'updated_at' => now()]
          );
        }

        // 画像情報のインポート
        if (!empty($cleanedRow[5])) {
          ShopImage::create([
            'shop_id' => $shop->id,
            'shop_image_url' => $cleanedRow[5],
            'created_at' => now(),
            'updated_at' => now(),
          ]);
        }

        return $shop; // Shopモデルを返す
      } catch (\Exception $e) {
        Log::error("店舗のインポート中にエラーが発生しました: " . $e->getMessage());
        throw $e; // エラーを再スローしてトランザクションをロールバック
      }
    });
  }

  // エラーメッセージの収集
  public function onFailure(\Throwable $e)
  {
    $errorMessage = $e->getMessage();
    $errorData = $e->getTrace()[0]['args'][0] ?? null; // エラーメッセージに含まれるデータを取得

    if ($errorData) {
      $errorMessage .= " (エラーが発生したデータ: " . json_encode($errorData) . ")";
    }

    session()->push('import_errors', $errorMessage);
  }
  public function batchSize(): int
  {
    return 500; // 一度にインポートするバッチサイズ
  }

  public function chunkSize(): int
  {
    return 500; // チャンクサイズ
  }
}
