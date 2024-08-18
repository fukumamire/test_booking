<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQrCodeTokenToBookings extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('bookings', function (Blueprint $table) {
      $table->string('qr_code_token')->unique()->nullable()->after('status'); // status列の後にqr_code_token列を追加
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('bookings', function (Blueprint $table) {
      $table->dropColumn('qr_code_token');
    });
  }
}
