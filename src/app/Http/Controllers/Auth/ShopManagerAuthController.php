<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ShopManagerAuthController extends Controller
{
  public function showLoginForm()
  {
    return view('auth.shop-manager-login');
  }

  public function login(Request $request)
  {
    // バリデーション
    $request->validate([
      'email' => 'required|email',
      'password' => 'required|min:8',
    ]);

    $credentials = $request->only(['email', 'password']);

    if (Auth::guard('shop_manager')->attempt($credentials)) {
      // セッションIDを再生成
      $request->session()->regenerate();

      return redirect()->back()->withInput($request->only('email'))
        ->withErrors([
          'email' => '入力されたログイン情報が正しくありません。',
        ]);
    }

    // ログイン成功後、shop-managerロールを持っているか確認
    $user = User::where('email', $request->input('email'))->first();

    if (
      !$user || !$user->hasRole('shop-manager')
    ) {
      Auth::logout();
      return redirect()->back()->withInput($request->only('email'))
        ->withErrors([
          'email' => '店舗代表者権限がありません。',
        ]);
    }

    // ログイン成功後に店舗代表者専用画面にリダイレクト
    return redirect()->intended('/shop-manager/dashboard');
  }

  public function logout(Request $request)
  {
    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect()->route('shop-manager.login');
  }
}
