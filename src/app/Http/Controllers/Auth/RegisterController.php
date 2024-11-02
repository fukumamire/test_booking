<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


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
        [
          'name.required' => '名前は必須項目です。',
          'name.string' => '名前は文字列で入力してください。',
          'name.max' => '名前は255文字以内で入力してください。',
          'email.required' => 'メールアドレスは必須項目です。',
          'email.string' => 'メールアドレスは文字列で入力してください。',
          'email.email' => '有効なメールアドレスを入力してください。',
          'email.max' => 'メールアドレスは255文字以内で入力してください。',
          'email.unique' => 'そのメールアドレスは既に使用されています。',
          'password.required' => 'パスワードは必須項目です。',
          'password.string' => 'パスワードは文字列で入力してください。',
          'password.min' => 'パスワードは最低8文字以上で入力してください。',
          'password.confirmed' => '確認用パスワードと一致しません。',
        ]

      ]);

      $createNewUser = new CreateNewUser();
      $user = $createNewUser->create($validatedData);

      // ユーザーが作成されたことを確認する
      if (!$user) {
        throw new \Exception('Failed to create user');
      }

      // メール認証通知を送信（Laravelの標準機能を使用）
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
