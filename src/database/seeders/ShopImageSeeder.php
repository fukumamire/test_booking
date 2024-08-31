<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopImageSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$existingIds = DB::table('shop_images')->pluck('id')->toArray();
		$imagesToInsert = [
			[
				'id' => 1,
				'shop_id' => 1,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/sushi.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 2,
				'shop_id' => 2,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/yakiniku.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 3,
				'shop_id' => 3,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/izakaya.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 4,
				'shop_id' => 4,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/italian.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 5,
				'shop_id' => 5,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/ramen.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 6,
				'shop_id' => 6,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/yakiniku.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 7,
				'shop_id' => 7,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/italian.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 8,
				'shop_id' => 8,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/ramen.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 9,
				'shop_id' => 9,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/izakaya.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 10,
				'shop_id' => 10,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/sushi.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 11,
				'shop_id' => 11,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/yakiniku.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 12,
				'shop_id' => 12,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/yakiniku.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 13,
				'shop_id' => 13,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/izakaya.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 14,
				'shop_id' => 14,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/sushi.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 15,
				'shop_id' => 15,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/ramen.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 16,
				'shop_id' => 16,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/izakaya.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 17,
				'shop_id' => 17,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/sushi.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 18,
				'shop_id' => 18,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/yakiniku.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 19,
				'shop_id' => 19,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/italian.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
			[
				'id' => 20,
				'shop_id' => 20,
				'shop_image_url' => 'https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/sushi.jpg',
				'created_at' => now(),
				'updated_at' => now(),
			],
		];

		$imagesToInsert = array_filter($imagesToInsert, function ($image) use ($existingIds) {
			return !in_array($image['id'], $existingIds);
		});

		if (!empty($imagesToInsert)) {
			DB::table('shop_images')->insert($imagesToInsert);
			echo "Added " . count($imagesToInsert) . " new shop images.\n";
		} else {
			echo "新しいshopの画像の追加はありません。\n";
		}
	}
}
