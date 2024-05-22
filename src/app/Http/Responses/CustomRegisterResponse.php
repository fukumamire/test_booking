<?php
namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;


class CustomRegisterResponse implements RegisterResponseContract
{
public function toResponse($request): Response
{
// リダイレクト先をカスタマイズ
return redirect('/thanks');
}
}