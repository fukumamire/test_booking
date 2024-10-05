<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;


class UsersController extends Controller
{
  //管理者が店舗代表者を作成するためのフォームを表示するビュー
  public function createShopManager(Request $request)
  {
    return view('admin.users.create-shop-manager');
  }

  public function storeShopManager(Request $request)
  {
    $validatedData = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:users',
      'password' => 'required|min:8',
    ]);

    // パスワードをハッシュ化
    $validatedData['password'] = Hash::make($validatedData['password']);

    $user = User::create($validatedData);
    $user->assignRole('shop-manager');

    return redirect()->route('users.shop-manager-done')->with('success', '新しい店舗代表者の登録が完了しました。');
  }
  //管理者画面からユーザー一覧（一般　管理者　店舗代表者）を表示する際　ユーザーデータを取得
  public function index()
  {
    $users = User::with('roles')->paginate(5);
    $roles = Role::all();
    return view('admin.users.user_index', compact('users', 'roles'));
  }
}
