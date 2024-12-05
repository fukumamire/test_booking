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

  private function cleanData($data)
  {
    return array_map(function ($value) {
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
    }, $data);
  }

  public function model(array $row): ?Shop
  {
    // ヘッダー行をスキップ
    if ($row[0] === '店舗名') {
      return null;
    }

    $cleanedRow = $this->cleanData($row);

    Log::info('Received row data:', $row);
    Log::info('Cleaned row data:', $cleanedRow);

    if (empty(trim($cleanedRow[0]))) {
      Log::warning("店舗名が空です。データ: " . json_encode($cleanedRow));
      return null;
    }

    // エリア情報のインポート
    $areaName = trim($row[2]);
    Log::debug("エリア名 (未処理): " . $areaName);

    // DEFINED_AREAS 配列を逆順にして短縮形から完全名へのマッピングを作成
    $reverseDefinedAreas = array_merge(self::DEFINED_AREAS, array_flip(self::DEFINED_AREAS));

    // 標準化処理の簡略化　入力されたエリア名をそのまま DEFINED_AREAS にマッピングし、マッチしない場合はエラーをスロー
    $standardizedAreaName = self::DEFINED_AREAS[$areaName] ?? null;

    if ($standardizedAreaName === null) {
      // エリア名が見つからない場合、逆マッピングを試みる
      $standardizedAreaName = $reverseDefinedAreas[$areaName] ?? null;

      if ($standardizedAreaName === null) {
        throw new \Exception("地域が不正です。入力された値: '$areaName'。許可された値は「東京」「大阪」「福岡」または「東京都」「大阪府」「福岡県」のみです。");
      }
    }

    return DB::transaction(function () use ($cleanedRow, $standardizedAreaName) {
      try {
        // 既存の店舗を検索
        $shop = Shop::updateOrCreate(
          ['name' => $cleanedRow[0]],
          [
            'outline' => $cleanedRow[4],
            'user_id' => !empty(trim($cleanedRow[1])) ? filter_var($cleanedRow[1], FILTER_VALIDATE_INT) : null,
            'created_at' => now(),
            'updated_at' => now(),
          ]
        );

        // エリア情報の関連付け
        $area = Area::where('name', $standardizedAreaName)->first();
        if (!$area) {
          throw new \Exception("地域が見つかりません。許可された値は「東京」「大阪」「福岡」または「東京都」「大阪府」「福岡県」のみです。");
        }
        Log::debug("エリア名 (標準化後): " . $standardizedAreaName);


        DB::table('shop_areas')->updateOrInsert(
          ['shop_id' => $shop->id, 'area_id' => $area->id],
          ['updated_at' => now()]
        );

        // ジャンル情報のインポート
        $genres = explode(',', $cleanedRow[3]);

        foreach ($genres as $genreName) {
          $standardizedGenreName = trim($genreName);

          // ジャンのバリデーション
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

        return $shop;
      } catch (\Exception $e) {
        Log::error("店舗のインポート中にエラーが発生しました: " . $e->getMessage());
        Log::error("エラーが発生したデータ: " . json_encode($cleanedRow));
        throw $e; // エラーを再スローしてトランザクションをロールバック
      }
    });
  }

  public function batchSize(): int
  {
    return 500; // 一度にインポートするバッチサイズ
  }

  public function chunkSize(): int
  {
    return 500; // チャンクサイズ
  }

  public function onError(\Throwable $e)
  {
    Log::error('Error importing shops: ' . $e->getMessage());
    session()->push('import_errors', 'An error occurred while importing shops. Please try again.');
  }
}
