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

class ShopImport implements ToModel, WithBatchInserts, WithChunkReading
{
  public function model(array $row)
  {
    return DB::transaction(function () use ($row) {
      // 店舗情報のインポート
      $shop = Shop::create([
        'name' => $row['店舗名'],
        'outline' => $row['店舗概要'] ?? '',
        'user_id' => $row['ユーザーID'] ?? null,
      ]);

      // エリア情報のインポート（存在しない場合は作成）
      $areaName = $row['地域'];
      $area = Area::firstOrCreate(['name' => $areaName]);

      // shop_areas テーブルへのインポート
      DB::table('shop_areas')->insert([
        'shop_id' => $shop->id,
        'area_id' => $area->id,
      ]);

      // ジャンル情報のインポート
      foreach (explode(',', $row['ジャンル']) as $genreName) {
        $genre = Genre::firstOrCreate(['name' => trim($genreName)], ['shop_id' => $shop->id]);
      }

      // 画像情報のインポート
      if (!empty($row['画像URL'])) {
        ShopImage::create([
          'shop_id' => $shop->id,
          'shop_image_url' => $row['画像URL']
        ]);
      }

      return $shop;
    });
  }

  public function batchSize(): int
  {
    return 1000;
  }

  public function chunkSize(): int
  {
    return 1000;
  }
}
