<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewReviewsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('new_reviews', function (Blueprint $table) {
      $table->id();
      $table->foreignId('shop_id')->constrained()->onDelete('cascade');
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->integer('rating')->nullable()->comment('評価（1から5までの値を格納）');
      $table->text('comment')->comment('レビューコメント（20文字以上400文字以内）');
      $table->string('image_url')->nullable();
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
    Schema::dropIfExists('new_reviews');
  }
}
