<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
  use HasFactory;

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
    ][$this->status] ?? '不明';
  }
}
