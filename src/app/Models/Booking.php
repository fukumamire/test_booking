<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
  use HasFactory, SoftDeletes;

  // 予約が属するユーザー
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  // 予約が関連する店舗
  public function shop()
  {
    return $this->belongsTo(Shop::class);
  }

  // 予約可能なフィールド
  protected $fillable = ['date', 'time', 'number_of_people', 'status'];

  // 予約の状態定数
  const STATUS_ACTIVE = 'active';
  const STATUS_CANCELLED = 'cancelled';
  const STATUS_COMPLETED = 'completed';

  // ステータスのゲッター
  public function getStatusAttribute()
  {
    return [
      self::STATUS_ACTIVE => '予約中',
      self::STATUS_CANCELLED => 'キャンセル済み',
      self::STATUS_COMPLETED => '完了',
    ][$this->attributes['status']] ?? '不明';
  }

  public function changes()
  {
    return $this->hasMany(BookingChange::class);
  }
}
