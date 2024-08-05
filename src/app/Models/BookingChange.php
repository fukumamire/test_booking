<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingChange extends Model
{
  use HasFactory;

  protected $fillable = [
    'booking_id',
    'user_id',
    'old_booking_date',
    'old_booking_time',
    'old_number_of_people',
    'new_booking_date',
    'new_booking_time',
    'new_number_of_people',
    'changed_at',
  ];

  public function booking()
  {
    return $this->belongsTo(Booking::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
