<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReviewsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('reviews', function (Blueprint $table) {
			// 既存のカラムを変更
			$table->integer('rating')->nullable()->change();

			// title カラムを削除
			$table->dropColumn('title');

			// 新しいカラムを追加
			$table->string('image_url')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('reviews', function (Blueprint $table) {
			// 変更前の状態に戻す
			$table->unsignedTinyInteger('rating')->default(1)->change();

			// 削除したカラムを復元
			$table->string('title', 20)->nullable()->comment('レビュータイトル（最大20文字）');

			// 追加したカラムを削除
			$table->dropColumn('image_url');
		});
	}
}
