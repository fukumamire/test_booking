<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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

    // 認証ガードの決定
    $guard = $this->getGuard($request);

    // 認証の試行
    if (!Auth::guard($guard)->attempt($credentials)) {
      $message = $this->getErrorMessage($request->input('email'));
      return redirect()->back()->withInput()->withErrors([$message]);
    }
    // セッションの再生成
    $request->session()->regenerate();
    // リダイレクト先の決定
    return $this->redirectTo($guard);
  }

  public function logout(Request $request)
  {
    $guard = $this->getGuard($request);

    Auth::guard($guard)->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
  }
  private function getGuard(Request $request)
  {
    // 管理者ログインかどうかの判断
    $user = User::where('email', $request->input('email'))->first();
    if ($user && $user->hasRole('super-admin')) {
      return 'admin';
    }
    return 'web';
  }

  private function getErrorMessage($email)
  {
    $user = User::where('email', $email)->first();

    if (!$user) {
      return '会員登録をしてください';
    } elseif ($user->hasRole('super-admin')) {
      return '管理者ログインはこちら';
    } else {
      return 'メールアドレスまたはパスワードが正しくありません。再度お試しください。';
    }
  }

  private function redirectTo($guard)
  {
    switch ($guard) {
      case 'admin':
        return redirect()->route('admin.login');
      default:
        return redirect()->route('register');
    }
  }
}
