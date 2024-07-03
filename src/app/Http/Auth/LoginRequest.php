namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Sanctum\HasApiTokens;

class LoginRequest extends FormRequest
{
// Fortifyのデフォルトのバリデーションや認証プロセスを利用しつつ、
// 認証後にSanctumのトークンを生成してレスポンスに含める処理を追加します。

/**
* Get the authenticated response.
*
* @param \Illuminate\Http\Request $request
* @return \Symfony\Component\HttpFoundation\Response|null
*/
public function authenticated(Request $request, $user): ?LoginResponse
{
return new LoginResponse($this->createToken($user));
}

protected function createToken($user)
{
$token = $user->createToken('auth-token')->plainTextToken;

return response()->json([
'access_token' => $token,
'token_type' => 'bearer',
]);
}
}