<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ShopImport implements ToArray, WithChunkReading, WithBatchInserts
{
  private $line = 0;

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

      // 空白を除去し、空文字列をnullに変換
      $value = is_string($value) ? trim($value) : $value;
      $value = $value === '' ? null : $value;

      return $value;
    }, array_keys($data), $data);
  }

  public function array(array $row)
  {
    $this->line++;
    $cleanedRow = $this->cleanData($row);

    Log::info("CSV データ: " . json_encode($row));
    Log::info("クリーンドデータ: " . json_encode($cleanedRow));

    if (empty($cleanedRow['店舗名'])) {
      Log::warning("店舗名が設定されていません。行: " . $this->line . ". データ: " . json_encode($cleanedRow));
      return null; // 空の行をスキップ
    }

    return [
      'name' => $cleanedRow['店舗名'],
      'outline' => $cleanedRow['店舗概要'] ?? '',
      'user_id' => !empty(trim($cleanedRow['ユーザーID'])) ? filter_var($cleanedRow['ユーザーID'], FILTER_VALIDATE_INT) : 1,
      'updated_at' => now(),
      'area_name' => $cleanedRow['地域'] ?? '',
      'genres' => !empty($cleanedRow['ジャンル']) ? explode(',', $cleanedRow['ジャンル']) : [], // カンマ区切りを配列に変換
      'image_url' => $cleanedRow['画像URL'] ?? '',
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
    session()->flash('error', 'インポート中にエラーが発生しました。詳細はログをご確認ください。');
  }
}
