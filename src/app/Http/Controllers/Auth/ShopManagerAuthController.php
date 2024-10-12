<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ShopManagerAuthController extends Controller
{
  public function __construct()
  {
    $this->middleware('guest:shop_manager')->except('logout');
  }

  public function showLoginForm()
  {
    return view('auth.shop-manager-login');
  }


  public function login(Request $request)
  {
    $request->validate([
      'email' => ['required', 'email'],
      'password' => ['required', 'min:8'],
    ]);

    $guard = 'shop_manager';

    if (!Auth::guard($guard)->attempt($request->only('email', 'password'))) {
      return back()->withErrors([
        'email' => '入力されたログイン情報が正しくありません。',
      ]);
    }

    $request->session()->regenerate();

    return $this->handlePostLogin($request);
  }

  private function handlePostLogin(Request $request)
  {
    $user = User::where('email', $request->input('email'))->first();

    if (!$user || !$user->isManager()) {
      Auth::guard('shop_manager')->logout();
      return redirect()->back()->withInput($request->only('email'))
        ->withErrors([
          'email' => '店舗代表者権限がありません。',
        ]);
    }

    return redirect()->route('shop-manager.dashboard');
  }


  public function logout(Request $request)
  {
    Auth::guard('shop_manager')->logout($request);
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
  }
}
