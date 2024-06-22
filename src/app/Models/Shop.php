<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
  use HasFactory;

  protected $fillable = ['name', 'outline'];

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

  public function favoritedBy()
  {
    return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
  }

  // ユーザーがその店舗をお気に入り登録しているか
  public function isFavoriteBy(User $user)
  {
    return $this->favoritedBy->contains($user->id);
  }
}
