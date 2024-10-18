<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopImage extends Model
{
  use HasFactory, SoftDeletes;
  protected $table = 'shop_images';
  protected $fillable = ['shop_image_url', 'shop_id'];
  protected $appends = ['url'];

  public function shop()
  {
    return $this->belongsTo(Shop::class);
  }

  // shop_image_url プロパティを追加（getter）
  public function getShopImageUrlAttribute($value)
  {
    // 旧パスと新パスをチェックし、適切なURLを返す
    if (
      strpos($value, 'storage/') === 0 || strpos($value, '/') === 0
    ) {
      return asset($value);
    } elseif (strpos($value, 'http://') === 0 || strpos($value, 'https://') === 0) {
      return $value;
    } else {
      return asset('storage/' . $value);
    }
  }

  // shop_image_url プロパティを追加（setter）
  public function setShopImageUrlAttribute($value)
  {
    // 画像を保存し、URLを設定
    if ($value instanceof \Illuminate\Http\UploadedFile) {
      $imageName = time() . '_' . $value->getClientOriginalName();
      $imagePath = Storage::putFileAs('public/shop_images', $value, $imageName);
      $this->attributes['shop_image_url'] = 'storage/shop_images/' . $imageName;
    } else {
      $this->attributes['shop_image_url'] = $value;
    }
  }

  // URL属性を追加
  public function getUrlAttribute()
  {
    return $this->shop_image_url;
  }
}
