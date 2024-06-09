<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call(ShopsTableSeeder::class);
		$this->call(ShopImageSeeder::class);
		$this->call(AreasTableSeeder::class);
		$this->call(ShopAreasSeeder::class,);

		// \App\Models\User::factory(10)->create();
	}
}
