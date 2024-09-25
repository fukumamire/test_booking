<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopImage extends Model
{
  use HasFactory;
  protected $table = 'shop_images';
  protected $fillable = ['shop_image_url', 'shop_id'];

  public function shop()
  {
    return $this->belongsTo(Shop::class);
  }

  // shop_image_url プロパティを追加（getter）
  public function getShopImageUrlAttribute($value)
  {
    return asset('storage/' . $value);
  }
}
