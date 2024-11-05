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
      return is_string($value) ? trim(mb_convert_kana($value, 'as')) : $value;
    }, $data);
  }

  public function model(array $row): ?Shop
  {
    $cleanedRow = $this->cleanData($row);

    if (empty(trim($row[0]))) {
      return null;
    }

    DB::transaction(function () use ($row) {
      try {
        $userId = filter_var($row[1], FILTER_VALIDATE_INT);
        if ($userId === false) {
          $userId = null;
        }

        $shop = Shop::firstOrCreate([
          'name' => $row[0],
          'outline' => $row[4],
          'user_id' => $userId,
        ]);

        // エリア情報のインポート（新規作成）
        $areaName = trim($row[2]);
        if (!empty($areaName)) {
          $area = Area::create(['name' => $areaName]);
          DB::table('shop_areas')->insert([
            'shop_id' => $shop->id,
            'area_id' => $area->id,
            'created_at' => now(),
            'updated_at' => now()
          ]);
        }

        // ジャンル情報のインポート（新規作成）
        $genres = explode(',', $row[3]);
        foreach ($genres as $genreName) {
          Genre::create([
            'shop_id' => $shop->id,
            'name' => trim($genreName),
            'created_at' => now(),
            'updated_at' => now()
          ]);
        }

        // 画像情報のインポート（新規作成）
        if (!empty($row[5])) {
          ShopImage::create([
            'shop_id' => $shop->id,
            'shop_image_url' => $row[5],
            'created_at' => now(),
            'updated_at' => now()
          ]);
        }

        return $shop;
      } catch (\Exception $e) {
        Log::error("店舗のインポート中にエラーが発生しました: " . $e->getMessage());
        return null;
      }
    });
  }


  public function batchSize(): int
  {
    return 1000; // 一度にインポートするバッチサイズ
  }

  public function chunkSize(): int
  {
    return 1000; // チャンクサイズ
  }
}

// class ShopImport implements ToModel, WithBatchInserts, WithChunkReading
// {
//   protected $columnMappings = [
//     'Shop' => [
//       'name' => '店舗名',
//       'user_id' => 'ユーザーＩＤ',
//       'outline' => '店舗概要',
//     ],
//     'Area' => [
//       'name' => '地域',
//     ],
//     'Genre' => [
//       'name' => 'ジャンル',
//     ],
//     'ShopImage' => [
//       'shop_image_url' => '画像URL',
//     ],
//   ];

//   private function cleanData($data)
//   {
//     return array_map(function ($value) {
//       return is_string($value) ? trim(mb_convert_kana($value, 'as')) : $value;
//     }, $data);
//   }

//   public function model(array $row)
//   {
//     $cleanedRow = $this->cleanData($row);
//     $mappedRow = [];

//     // Shop モデルのカラム名を使用
//     foreach ($this->columnMappings['Shop'] as $key => $columnName) {
//       $mappedRow[$key] = $row[array_search($columnName, array_values($this->columnMappings['Shop']))] ?? '';
//     }

//     // 店舗名が空の場合はスキップ
//     if (empty(trim($mappedRow['name']))) {
//       return null; // 空の行はスキップ
//     }

//     return DB::transaction(function () use ($mappedRow) {
//       // ユーザーIDを整数に変換（不適切な値の場合、nullにする）
//       $userId = filter_var($mappedRow['userId'], FILTER_VALIDATE_INT);
//       if ($userId === false) {
//         $userId = null;
//       }

//       // 店舗情報のインポート
//       $shop = Shop::create([
//         'name' => $mappedRow['name'],
//         'outline' => $mappedRow['outline'],
//         'user_id' => $userId,
//       ]);

//       // エリア情報のインポート（存在しない場合は作成）
//       $areaName = trim($mappedRow['area']);
//       if (!empty($areaName)) {
//         $area = Area::firstOrCreate(['name' => $areaName]);
//         // shop_areas テーブルへのインポート
//         DB::table('shop_areas')->insert([
//           'shop_id' => $shop->id,
//           'area_id' => $area->id,
//         ]);
//       }

//       // ジャンル情報のインポート
//       $genres = explode(',', $mappedRow['genres']);
//       foreach ($genres as $genreName) {
//         $genre = Genre::firstOrCreate(['name' => trim($genreName)]);

//         DB::table('genres')->insert([
//           'shop_id' => $shop->id,
//           'name' => $genre->name,
//           'created_at' => now(),
//           'updated_at' => now(),
//           'deleted_at' => null,
//         ]);
//       }

//       // 画像情報のインポート
//       if (!empty($mappedRow['imageUrl'])) {
//         ShopImage::create([
//           'shop_id' => $shop->id,
//           'shop_image_url' => $mappedRow['imageUrl']
//         ]);
//       }

//       return $shop;
//     });
//   }

//   public function batchSize(): int
//   {
//     return 1000; // 一度にインポートするバッチサイズ
//   }

//   public function chunkSize(): int
//   {
//     return 1000; // チャンクサイズ
//   }
// }

// class ShopImport implements ToModel, WithBatchInserts, WithChunkReading
// {
//   public function model(array $row)
//   {
//     // カラム番号を使用してデータを取得
//     $mappedRow = [
//       'name' => $row[0] ?? '',
//       'userId' => $row[1] ?? '',
//       'area' => $row[2] ?? '',
//       'genres' => $row[3] ?? '',
//       'outline' => $row[4] ?? '',
//       'imageUrl' => $row[5] ?? '',
//     ];

//     // 店舗名が空の場合はエラーを投げる
//     if (empty(trim($mappedRow['name']))) {
//       throw new \Exception("店舗名が設定されていません。");
//     }

//     return DB::transaction(function () use ($mappedRow) {
//       // 店舗情報のインポート
//       $shop = Shop::create([
//         'name' => $mappedRow['name'],
//         'outline' => $mappedRow['outline'],
//         'user_id' => !empty($mappedRow['userId']) ? $mappedRow['userId'] : null,
//       ]);

//       // エリア情報のインポート（存在しない場合は作成）
//       $areaName = trim($mappedRow['area']);
//       if (!empty($areaName)) {
//         $area = Area::firstOrCreate(['name' => $areaName]);
//         // shop_areas テーブルへのインポート
//         DB::table('shop_areas')->insert([
//           'shop_id' => $shop->id,
//           'area_id' => $area->id,
//         ]);
//       }

//       // ジャンル情報のインポート
//       $genres = explode(',', $mappedRow['genres']);
//       foreach ($genres as $genreName) {
//         $genre = Genre::firstOrCreate(['name' => trim($genreName)]);

//         DB::table('genres')->insert([
//           'shop_id' => $shop->id,
//           'name' => $genre->name,
//           'created_at' => now(),
//           'updated_at' => now(),
//           'deleted_at' => null,
//         ]);
//       }

//       // 画像情報のインポート
//       if (!empty($mappedRow['imageUrl'])) {
//         ShopImage::create([
//           'shop_id' => $shop->id,
//           'shop_image_url' => $mappedRow['imageUrl']
//         ]);
//       }

//       return $shop;
//     });
//   }

//   public function batchSize(): int
//   {
//     return 1000; // 一度にインポートするバッチサイズ
//   }

//   public function chunkSize(): int
//   {
//     return 1000; // チャンクサイズ
//   }
// }

// class ShopImport implements ToModel, WithBatchInserts, WithChunkReading
// {
//   public function model(array $row)
//   {
//     // 列名のマッピング
//     $columnMapping = [
//       'name' => '店舗名',
//       'userId' => 'ユーザーID',
//       'area' => '地域',
//       'genres' => 'ジャンル',
//       'outline' => '店舗概要',
//       'imageUrl' => '画像URL',
//     ];

//     // 行データをマッピング
//     $mappedRow = [];
//     foreach ($columnMapping as $key => $columnName) {
//       $mappedRow[$key] = $row[$columnName] ?? '';
//     }

//     return DB::transaction(function () use ($mappedRow) {
//       // 店舗情報のインポート
//       $shop = Shop::create([
//         'name' => $mappedRow['name'],
//         'outline' => $mappedRow['outline'],
//         'user_id' => !empty($mappedRow['userId']) ? $mappedRow['userId'] : null,
//       ]);

//       if (!$shop->id) {
//         throw new \Exception("店舗IDが生成されませんでした。");
//       }

//       // エリア情報のインポート（存在しない場合は作成）
//       $areaName = trim($mappedRow['area']);
//       if (!empty($areaName)) {
//         $area = Area::firstOrCreate(['name' => $areaName]);
//         // shop_areas テーブルへのインポート
//         DB::table('shop_areas')->insert([
//           'shop_id' => $shop->id,
//           'area_id' => $area->id,
//         ]);
//       }

//       // ジャンル情報のインポート
//       $genres = explode(',', $mappedRow['genres']);
//       foreach ($genres as $genreName) {
//         $genre = Genre::firstOrCreate([
//           'name' => trim($genreName),
//         ]);

//         if (!empty($shop->id)) {
//           // ジャンルと店舗の関連付け（直接 genres テーブルに挿入）
//           DB::table('genres')->insert([
//             'shop_id' => $shop->id,
//             'name' => $genre->name,
//             'created_at' => now(),
//             'updated_at' => now(),
//             'deleted_at' => null,
//           ]);
//         } else {
//           // shop_id が空の場合はエラーをスロー
//           throw new \Exception("店舗IDが見つかりません。");
//         }
//       }


//       // 画像情報のインポート
//       if (!empty($mappedRow['imageUrl'])) {
//         ShopImage::create([
//           'shop_id' => $shop->id,
//           'shop_image_url' => $mappedRow['imageUrl']
//         ]);
//       }

//       return $shop;
//     });
//   }

//   public function batchSize(): int
//   {
//     return 1000; // 一度にインポートするバッチサイズ
//   }

//   public function chunkSize(): int
//   {
//     return 1000; // チャンクサイズ
//   }
// }

// namespace App\Imports;

// use App\Models\Shop;
// use App\Models\Area;
// use App\Models\Genre;
// use App\Models\ShopImage;
// use Illuminate\Support\Facades\DB;
// use Maatwebsite\Excel\Concerns\ToModel;
// use Maatwebsite\Excel\Concerns\WithBatchInserts;
// use Maatwebsite\Excel\Concerns\WithChunkReading;
// use Maatwebsite\Excel\Concerns\SkipsFailures;
// use Maatwebsite\Excel\Concerns\WithValidation;

// class ShopImport implements ToModel, WithBatchInserts, WithChunkReading, SkipsFailures, WithValidation
// {
//   public function model(array $row)
//   {
//     return DB::transaction(function () use ($row) {
//       // 店舗情報のインポート
//       $shop = Shop::create([
//         'name' => $row['店舗名'],
//         'outline' => $row['店舗概要'] ?? '',
//         'user_id' => $row['ユーザーID'] ?? null,
//       ]);

//       // エリア情報のインポート（存在しない場合は作成）
//       $areaName = $row['地域'];
//       $area = Area::firstOrCreate(['name' => $areaName]);

//       // shop_areas テーブルへのインポート
//       DB::table('shop_areas')->insert([
//         'shop_id' => $shop->id,
//         'area_id' => $area->id,
//       ]);

//       // ジャンル情報のインポート
//       $genres = explode(',', $row['ジャンル']);
//       foreach ($genres as $genreName) {
//         $genre = Genre::firstOrCreate(['name' => trim($genreName)]);
//         // ジャンルと店舗の関連付け
//         $shop->genres()->attach($genre->id);
//       }

//       // 画像情報のインポート
//       if (!empty($row['画像URL'])) {
//         $imageUrl = $row['画像URL'];
//         if ($this->isValidImageUrl($imageUrl)) {
//           ShopImage::create([
//             'shop_id' => $shop->id,
//             'shop_image_url' => $imageUrl
//           ]);
//         } else {
//           // 画像URLが無効な場合、エラーをスロー
//           throw new \Exception('画像URLは有効な画像ファイルを指定してください');
//         }
//       }

//       return $shop;
//     });
//   }

//   public function rules(): array
//   {
//     return [
//       '*.店舗名' => 'required|max:50',
//       '*.地域' => 'required|in:東京都,大阪府,福岡県',
//       '*.ジャンル' => 'required',
//       '*.ジャンル.*' => 'in:寿司,焼肉,イタリアン,居酒屋,ラーメン',
//       '*.店舗概要' => 'max:400',
//       '*.画像URL' =>
//       'nullable|url|mimes:jpeg,png',
//     ];
//   }

//   public function customValidationMessages()
//   {
//     return [
//       '*.店舗名.required' => '店舗名は必須です',
//       '*.店舗名.max' => '店舗名は50文字以内で入力してください',
//       '*.地域.required' => '地域は必須です',
//       '*.地域.in' => '地域は「東京都」「大阪府」「福岡県」のいずれかを選択してください',
//       '*.ジャンル.required' => 'ジャンルは必須です',
//       '*.ジャンル.*.in' => 'ジャンルは「寿司」「焼肉」「イタリアン」「居酒屋」「ラーメン」のいずれかを選択してください',
//       '*.店舗概要.max' => '店舗概要は400文字以内で入力してください',
//       '*.画像URL.url' => '画像URLは有効なURLを指定してください',
//       '*.画像URL.mimes' => '画像URLはJPG、PNG形式のファイルを指定してください',
//     ];
//   }

//   public function batchSize(): int
//   {
//     return 1000; // 一度にインポートするバッチサイズ
//   }

//   public function chunkSize(): int
//   {
//     return 1000; // チャンクサイズ
//   }

//   protected function isValidImageUrl($url)
//   {
//     return filter_var($url, FILTER_VALIDATE_URL) && preg_match('/\.(jpeg|png)$/', $url);
//   }
// }
