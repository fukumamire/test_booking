<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveGenreIdFromShopsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('shops', function (Blueprint $table) {
      $table->dropForeign(['genre_id']); // 外部キー制約を削除
      $table->dropColumn('genre_id'); // カラムを削除
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('shops', function (Blueprint $table) {
      $table->unsignedBigInteger('genre_id')->nullable()->after('id'); // カラムを再追加
      $table->foreign('genre_id')->references('id')->on('genres'); // 外部キー制約を再追加
    });
  }
}
