<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;


class AdminLoginController extends Controller
{

  public function showLoginForm()
  {
    return view('admin.auth.login');
  }

  public function login(Request $request)
  {
    $credentials = $request->only('email', 'password');

    // ログイン処理
    if (!Auth::guard('admin')->attempt($credentials)) {
      return redirect()->back()->withInput($request->only('email'))
        ->withErrors([
          'email' => '入力されたログイン情報が正しくありません。',
        ]);
    }

    // ログイン成功後、super-adminロールを持っているか確認
    $user = User::where('email', $request->input('email'))->first();

    if (!$user || !$user->hasRole('super-admin')) {
      Auth::guard('admin')->logout();
      return redirect()->back()->withInput($request->only('email'))
        ->withErrors([
          'email' => '管理者権限がありません。',
        ]);
    }

    // ログイン成功後に管理者専用画面にリダイレクト
    return redirect()->intended('/admin/index');
  }

  public function logout(Request $request)
  {
    Auth::guard('admin')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect()->route('admin.login');
  }
}
