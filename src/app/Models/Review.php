<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Review extends Model
{
  use HasFactory;
  protected $fillable = [
    'rating',
    'comment',
    'image_url'
  ];

  public function shop()
  {
    return $this->belongsTo(Shop::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
