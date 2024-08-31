<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
	public function run()
	{
		$this->call([
			ShopsTableSeeder::class,
			ShopImageSeeder::class,
			AreasTableSeeder::class,
			ShopAreasSeeder::class,
			GenresTableSeeder::class,
		]);

		// 管理者を作成（既存の場合はスキップ）
		if (!User::where('email', 'admin@example.com')->exists()) {
			$this->createAdminUser();
		}

		// 店舗代表者を作成（既存の場合はスキップ）
		if (!User::where('email', 'shop-manager@example.com')->exists()) {
			$this->createShopManagerUser();
		}
	}

	private function createAdminUser()
	{
		$adminId = User::create([
			'name' => 'Admin',
			'email' => 'admin@example.com',
			'password' => Hash::make('password'),
		])->id;

		$superAdminRole = \Spatie\Permission\Models\Role::create(['name' => 'super-admin']);
		DB::table('model_has_roles')->insert([
			'role_id' => $superAdminRole->id,
			'model_type' => 'App\Models\User',
			'model_id' => $adminId,
		]);
	}

	private function createShopManagerUser()
	{
		$shopManagerId = User::create([
			'name' => 'Shop Manager',
			'email' => 'shop-manager@example.com',
			'password' => Hash::make('password'),
		])->id;

		$shopManagerRole = \Spatie\Permission\Models\Role::create(['name' => 'shop-manager']);
		DB::table('model_has_roles')->insert([
			'role_id' => $shopManagerRole->id,
			'model_type' => 'App\Models\User',
			'model_id' => $shopManagerId,
		]);
	}
}
