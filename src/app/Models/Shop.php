<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;


class Shop extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = ['name', 'user_id', 'outline'];

  public static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      if (!$model->name) {
        throw new \Exception("店舗名が設定されていません。");
      }
    });
  }

  public function user()
  {
    return $this->hasOne(User::class);
  }

  public function areas()
  {
    return $this->belongsToMany(Area::class, 'shop_areas')->withTimestamps();
  }


  public function genres()
  {
    return $this->hasMany(Genre::class);
  }

  public function images()
  {
    return $this->hasMany(ShopImage::class);
  }

  // 予約が関連する店舗
  public function bookings()
  {
    return $this->hasMany(Booking::class);
  }


  // Favorite モデルを通じて User モデルとの関連付けを定義
  public function favorites()
  {
    return $this->hasMany(Favorite::class);
  }

  // User モデルとの関連付けを Favorite モデルを通じて行う
  public function favoritedBy()
  {
    return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
  }


  // ユーザーがお気に入り登録しているかどうかを確認　特定のユーザーに対してお気に入り状態を判定する
  public function isFavoriteBy(User $user)
  {
    return $this->favorites()->where('user_id', $user->id)->exists();
  }

  // お気に入り登録・解除の処理
  public function toggleFavorite(User $user)
  {
    $favorite = $this->favorites()->where('user_id', $user->id)->first();

    if ($favorite) {
      $favorite->delete(); // お気に入り解除
      $this->is_favorite = false;
    } else {
      $this->favorites()->create(['user_id' => $user->id]); // お気に入り登録
    }
  }
}
