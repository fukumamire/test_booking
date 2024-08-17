<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('reviews', function (Blueprint $table) {
      $table->id();
      $table->foreignId('shop_id')->constrained()->onDelete('cascade'); // 店舗ID。店舗が削除された場合、関連するレビューも削除される
      $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ユーザーID。ユーザーが削除された場合、関連するレビューも削除される
      $table->unsignedTinyInteger('rating')->default(1)->comment('5段階評価（1から5）'); // 評価（1から5までの値を格納）
      $table->string('title', 20)->nullable()->comment('レビュータイトル（最大20文字）'); // タイトルは任意で、最大20文字
      $table->text('comment')->comment('レビューコメント（20文字以上400文字以内）'); // コメントは必須で、20文字以上400文字以内
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('reviews');
  }
}
