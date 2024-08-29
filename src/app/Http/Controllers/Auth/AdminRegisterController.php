<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRegisterController extends Controller
{
  public function __construct()
  {
    $this->middleware('guest:admin');
  }

  public function showRegistrationForm()
  {
    return view('admin.auth.register');
  }

  public function register(Request $request)
  {
    $validatedData = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create($validatedData);

    Auth::guard('admin')->login($user);

    return redirect()->intended('admin.dashboard');
  }
}
