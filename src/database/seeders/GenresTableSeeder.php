<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Genre;


class GenresTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $genres = [
      ['shop_id' => 1, 'name' => '寿司'],
      ['shop_id' => 2, 'name' => '焼肉'],
      ['shop_id' => 3, 'name' => '居酒屋'],
      ['shop_id' => 4, 'name' => 'イタリアン'],
      ['shop_id' => 5, 'name' => 'ラーメン'],
      ['shop_id' => 6, 'name' => '焼肉'],
      ['shop_id' => 7, 'name' => 'イタリアン'],
      ['shop_id' => 8, 'name' => 'ラーメン'],
      ['shop_id' => 9, 'name' => '居酒屋'],
      ['shop_id' => 10, 'name' => '寿司'],
      ['shop_id' => 11, 'name' => '焼肉'],
      ['shop_id' => 12, 'name' => '焼肉'],
      ['shop_id' => 13, 'name' => '居酒屋'],
      ['shop_id' => 14, 'name' => '寿司'],
      ['shop_id' => 15, 'name' => 'ラーメン'],
      ['shop_id' => 16, 'name' => '居酒屋'],
      ['shop_id' => 17, 'name' => '寿司'],
      ['shop_id' => 18, 'name' => '焼肉'],
      ['shop_id' => 19, 'name' => 'イタリアン'],
      ['shop_id' => 20, 'name' => '寿司'],
    ];

    foreach ($genres as $genre) {
      Genre::create($genre);
    }
  }
}
