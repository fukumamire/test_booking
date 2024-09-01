<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Spatie\Permission\Models\Role;

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

    // super-admin ロールを取得または作成
    $superAdminRole = Role::where('name', 'super-admin')->firstOrCreate(['name' => 'super-admin']);
    
    // ユーザーに super-admin ロールを割り当て
    $user->assignRole($superAdminRole);

    Auth::guard('admin')->login($user);

    return redirect()->intended('admin.dashboard');
  }
}
