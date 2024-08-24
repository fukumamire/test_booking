<?php

namespace App\Http\Controllers\auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;


class AdminAuthController extends Controller
{
  protected $redirectTo = '/admin/dashboard';

  public function showLoginForm()
  {
    return view('admin.auth.login');
  }

  public function login(Request $request)
  {
    $credentials = $request->only('email', 'password');

    if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
      return redirect()->intended($this->redirectPath());
    }

    return back()->withErrors([
      'email' => '入力されたログイン情報が正しくありません。',
    ]);
  }

  protected function validateLogin(Request $request)
  {
    $this->validate($request, [
      'email' => 'required|string|email|max:255',
      'password' => 'required|string|min:8',
    ]);
  }

  protected function guard()
  {
    return Auth::guard('admin');
  }
}
