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
    // 旧パスと新パスをチェックし、適切なURLを返す
    if (strpos($value, 'storage/') === 0) {
      return asset($value);
    } elseif (strpos($value, 'http://') === 0 || strpos($value, 'https://') === 0) {
      return $value;
    } else {
      return asset('storage/' . $value);
    }
  }
}
