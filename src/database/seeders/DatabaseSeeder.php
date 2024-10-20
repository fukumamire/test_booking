<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

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
		DB::transaction(function () {
			$adminId = User::create([
				'name' => 'Admin',
				'email' => 'admin@example.com',
				'password' => Hash::make('password'),
			])->id;

			$superAdminRole =  \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super-admin']);
			$admin = User::find($adminId);

			// 既存の関連付けを削除してから、新しい関連付けを追加
			$admin->removeRole($superAdminRole);
			$admin->assignRole($superAdminRole);

			echo "Admin user created and assigned super-admin role\n";
		});
	}

	private function createShopManagerUser()
	{
		DB::transaction(function () {
			$shopManagerId = User::create([
				'name' => 'Shop Manager',
				'email' => 'shop-manager@example.com',
				'password' => Hash::make('password'),
			])->id;

			$shopManagerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'shop-manager']);

			$shopManager = User::find($shopManagerId);

			// 既存の関連付けを削除してから、新しい関連付けを追加
			$shopManager->removeRole($shopManagerRole);
			$shopManager->assignRole($shopManagerRole);

			echo "Shop Manager user created and assigned shop-manager role\n";
		});
	}
}
