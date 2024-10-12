<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Shop;
use App\Models\Favorite;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable, HasRoles;
  /**
   * Guard name for Spatie roles.
   *
   * @var string
   */

  public function getGuardName()
  {
    // 条件によってガード名を動的に返す
    if ($this->hasRole('shop-manager')) {
      return 'shop-manager';
    } elseif ($this->hasRole('admin')) {
      return 'admin';
    }

    return 'web'; // デフォルトは web ガード
  }

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'shop_id',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function isAdmin()
  {
    return $this->hasRole('super-admin');
  }

  public function isManager()
  {
    return $this->hasRole('shop-manager');
  }


  public function shop()
  {
    return $this->belongsTo(Shop::class);
  }

  // Favorite モデルを通じて Shop モデルとの関連付け
  public function favorites()
  {
    return $this->hasMany(Favorite::class, 'user_id');
  }

  // お気に入り登録
  public function favorite(Shop $shop)
  {
    $this->favorites()->create(['shop_id' => $shop->id]);
  }

  // お気に入り解除
  public function unfavorite(Shop $shop)
  {
    $this->favorites()->where('shop_id', $shop->id)->delete();
  }

  public function toggleFavorite(Shop $shop)
  {
    $favorite = $this->favorites()->where('shop_id', $shop->id)->first();

    if ($favorite) {
      $favorite->delete();
    } else {
      $this->favorites()->create(['shop_id' => $shop->id]);
    }
  }

  // お気に入り登録しているかどうかを確認
  public function hasFavorited(Shop $shop)
  {
    return $this->favorites()->where('shop_id', $shop->id)->exists();
  }

  // 予約変更
  public function bookingChanges()
  {
    return $this->hasMany(BookingChange::class);
  }
}
