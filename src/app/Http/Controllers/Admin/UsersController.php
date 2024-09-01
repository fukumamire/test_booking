<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
  //店舗代表者を作成するためのフォームを表示するビューを返す
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

    $user = User::create($validatedData);
    $user->assignRole('shop-manager');

    return redirect()->route('users.shop-manager-done')->with('success', '新しい店舗代表者の登録が完了しました。');
  }
}
