<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;


class LogoutController extends Controller
{
  public function logout(Request $request)
  {
    // Sanctumのトークンを削除してログアウトする
    $request->user()->currentAccessToken()->delete();

    // Fortifyによるログアウト処理（セッションの破棄など）
    Auth::logout();

    return response()->json(['message' => 'Logged out']);
  }
}
