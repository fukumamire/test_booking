<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Shop;
use App\Models\Favorite;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

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

  public function favorites()
  {
    return $this->hasMany(Favorite::class);
  }

  public function favorite(Shop $shop)
  {
    $this->favorites()->create(['shop_id' => $shop->id]);
  }

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

  public function hasFavorited(Shop $shop)
  {
    return $this->favorites()->where('shop_id', $shop->id)->exists();
  }
}
