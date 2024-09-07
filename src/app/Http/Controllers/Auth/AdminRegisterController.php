<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Hash;

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

  public function
  store(Request $request)
  {
    if (!method_exists($this, 'store')) {
      abort(500, 'store method does not exist.');
    }

    $validatedData = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
    ]);

    // パスワードをハッシュ化
    $validatedData['password'] = Hash::make($validatedData['password']);

    $user = User::create($validatedData);

    // super-admin ロールを取得または作成
    $superAdminRole = Role::where('name', 'super-admin')->firstOrCreate(['name' => 'super-admin']);

    // ユーザーに super-admin ロールを割り当て
    $user->assignRole($superAdminRole);

    Auth::guard('admin')->login($user);

    return redirect()->route('admin.index')->with('success', '新しい管理者を登録しました。'); // 管理者用のホームページへリダイレクト
  }
}
