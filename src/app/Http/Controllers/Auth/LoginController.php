<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
  public function __construct()
  {
    $this->middleware('guest');
  }

  public function showLoginForm()
  {
    return view('auth.login');
  }

  public function login(Request $request)
  {
    // 入力値のバリデーション
    $credentials = $request->validate([
      'email' => ['required', 'email'],
      'password' => ['required'],
    ]);

    // 認証の試行
    if (!Auth::attempt($credentials)) {
      // 認証失敗時の処理
      return redirect()->back()->withErrors(['認証に失敗しました。入力内容を確認してください。']);
    }
    // セッションの再生成
    $request->session()->regenerate();

    return redirect()->route('home');
  }

  public function logout(Request $request)
  {
    if ($this->guard() === 'admin') {
      Auth::guard('admin')->logout();
    } else {
      Auth::guard()->logout();
    }

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
  }
  private function guard()
  {
    return session()->get('guard') ?: 'web';
  }
}
