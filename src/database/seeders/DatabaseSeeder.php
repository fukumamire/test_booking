<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
		$this->call(ShopAreasSeeder::class);
		$this->call(GenresTableSeeder::class);
		// \App\Models\User::factory(10)->create();

		// 管理者を作成（既存の場合はスキップ）
		if (!DB::table('users')->where('email', 'admin@example.com')->exists()) {
			$this->createAdminUser();
		}

		// 店舗代表者を作成（既存の場合はスキップ）
		if (!DB::table('users')->where('email', 'shop-manager@example.com')->exists()) {
			$this->createShopManagerUser();
		}
	}

	private function createAdminUser()
	{
		$adminId = DB::table('users')->insertGetId([
			'name' => 'Admin',
			'email' => 'admin@example.com',
			'password' => Hash::make('password'),
		]);

		$superAdminRole = DB::table('roles')->firstOrCreate(['name' => 'super-admin']);
		DB::table('model_has_roles')->updateOrInsert([
			'role_id' => $superAdminRole->id,
			'model_type' => 'App\Models\User',
			'model_id' => $adminId,
		], [
			'role_id' => $superAdminRole->id,
			'model_type' => 'App\Models\User',
			'model_id' => $adminId,
		]);
	}

	private function createShopManagerUser()
	{
		$shopManagerId = DB::table('users')->insertGetId([
			'name' => 'Shop Manager',
			'email' => 'shop-manager@example.com',
			'password' => Hash::make('password'),
		]);

		$shopManagerRole = DB::table('roles')->firstOrCreate(['name' => 'shop-manager']);
		DB::table('model_has_roles')->updateOrInsert([
			'role_id' => $shopManagerRole->id,
			'model_type' => 'App\Models\User',
			'model_id' => $shopManagerId,
		], [
			'role_id' => $shopManagerRole->id,
			'model_type' => 'App\Models\User',
			'model_id' => $shopManagerId,
		]);
	}
}
