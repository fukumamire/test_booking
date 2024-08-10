<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingChangesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('booking_changes', function (Blueprint $table) {
      $table->id();
      $table->foreignId('booking_id')->constrained()->onDelete('cascade');
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->date('old_booking_date');
      $table->time('old_booking_time');
      $table->integer('old_number_of_people');
      $table->date('new_booking_date');
      $table->time('new_booking_time');
      $table->integer('new_number_of_people');
      $table->timestamp('changed_at')->nullable();
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
    Schema::dropIfExists('booking_changes');
  }
}
