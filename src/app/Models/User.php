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
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
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
