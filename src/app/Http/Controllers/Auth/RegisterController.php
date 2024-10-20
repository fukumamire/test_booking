<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegisterController extends Controller
{
  public function __construct()
  {
    $this->middleware('guest');
  }

  public function showRegistrationForm()
  {
    return view('auth.register');
  }

  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = app(\App\Actions\Fortify\CreateNewUser::class)->create($validatedData);

    //  ログイン処理は行わず、thanks画面にリダイレクト→このコードは不要Auth::guard('web')->login($user);

    return redirect()->route('thanks');
  }
}
