<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Shop extends Model
{
  use HasFactory;

  protected $fillable = ['name', 'outline', 'is_favorite'];

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

  public function isFavoriteBy(User $user)
  {
    return $this->favoritedBy->contains($user->id);
  }

  public function toggleFavorite()
  {
    $user = Auth::user();

    if ($user) {
      if ($user->favorites->where('shop_id', $this->id)->exists()) {
        $user->favorites->detach($this->id);
        $this->is_favorite = false;
      } else {
        $user->favorites->attach($this->id);
        $this->is_favorite = true;
      }
      $this->save();
    }
  }
}
