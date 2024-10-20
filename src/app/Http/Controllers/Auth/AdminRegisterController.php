<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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

  public function
  store(Request $request)
  {
    if (!method_exists($this, 'store')) {
      abort(500, 'store method does not exist.');
    }
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
      'password_confirmation' => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator)
        ->withInput();
    }

    try {
      DB::transaction(function () use ($request) {
        $user = app(\App\Actions\Fortify\CreateNewUser::class)->create(array_merge($request->all(), ['guard' => 'admin']));

        Auth::guard('admin')->login($user);
      });

      return redirect()->route('admin.index')
      ->with('success', '新しい管理者を登録しました。');
    } catch (\Exception $e) {
      return redirect()->back()
        ->withInput()
        ->withErrors(['error' => '登録中にエラーが発生しました。再度試してください。']);
    } 
  }
}
