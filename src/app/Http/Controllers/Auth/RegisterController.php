<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Log;


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
    try {
      $validatedData = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
      ]);

      $createNewUser = new CreateNewUser();
      $user = $createNewUser->create($validatedData);

      // ユーザーが作成されたことを確認する
      if (!$user) {
        throw new \Exception('Failed to create user');
      }

      $user->sendEmailVerificationNotification();

      return redirect()->route('thanks')->withSuccess('ユーザーの登録とメール認証の通知が送信されました。');
    } catch (\Exception $e) {
      Log::error('Error occurred during registration', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);

      return redirect()->back()->withInput()->withErrors(['エラーが発生しました。再度お試しください。']);
    }
  }
  
}
