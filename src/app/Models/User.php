<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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

  public function favorite(Shop $shop)
  {
    $this->favorites()->attach($shop->id);
  }

  public function unfavorite(Shop $shop)
  {
    $this->favorites()->detach($shop->id);
  }

  public function toggleFavorite(Shop $shop)
  {
    if ($this->hasFavorited($shop)) {
      $this->unfavorite($shop);
    } else {
      $this->favorite($shop);
    }
  }

  public function hasFavorited(Shop $shop)
  {
    return $this->favorites->contains($shop->id);
  }
}
