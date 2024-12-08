<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ShopImport implements ToArray, WithChunkReading, WithBatchInserts
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

  private function cleanData(array $data)
  {
    // id 列を削除（主キーはデータベースで自動生成）
    if (isset($data['id'])) {
      unset($data['id']);
    }

    return array_map(function ($key, $value) {
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

  public function array(array $row)
  {
    $cleanedRow = $this->cleanData($row);

    return [
      // 'id' => $row['id'] ?? null,
      'name' => $cleanedRow[0],
      'outline' => $cleanedRow[4],
      'user_id' => !empty(trim($cleanedRow[1])) ? filter_var($cleanedRow[1], FILTER_VALIDATE_INT) : 1,
      'updated_at' => now(),
      'area_name' => $cleanedRow[2],
      'genres' => [$cleanedRow[3]], // ジャンルは配列として扱う
      'image_url' => $cleanedRow[5],
    ];
  }

  public function chunkSize(): int
  {
    return 200;
  }

  public function batchSize(): int
  {
    return 500;
  }

  public function onError(\Throwable $e)
  {
    Log::error("インポート中にエラーが発生しました: " . $e->getMessage());
    Log::error("エラーの詳細: " . $e->getTraceAsString());
  }
}
