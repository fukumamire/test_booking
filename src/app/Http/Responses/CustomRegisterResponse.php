<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;


class CustomRegisterResponse implements RegisterResponseContract
{
  public function toResponse($request): Response
  {
    // リダイレクト先をカスタマイズ

    // ユーザーが管理者（super-admin）かどうかをチェック
    if ($request->user() && $request->user()->hasRole('super-admin')) {
      return redirect('/admin/login'); // 管理者用のリダイレクト先 管理者用のログイン画面
    }
    // 一般ユーザーの場合
    return redirect('/thanks');
  }
}
