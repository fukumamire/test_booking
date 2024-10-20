<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
  use PasswordValidationRules;

  /**
   * Validate and create a newly registered user.
   *
   * @param  array<string, string>  $input
   */
  public function create(array $input): User
  {
    // バリデーション
    Validator::make($input, [
      'name' => ['required', 'string', 'max:191'],
      'email' => [
        'required',
        'string',
        'email',
        'max:191',
        Rule::unique(User::class),
      ],
      'password' => $this->passwordRules(),
    ])->validate();

    // ユーザー作成
    $user = User::create([
      'name' => $input['name'],
      'email' => $input['email'],
      'password' => Hash::make($input['password']),
    ]);

    // ロールの割り当て
    if (isset($input['guard']) && $input['guard'] === 'admin') {
      $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
      $user->assignRole($superAdminRole);
    } else {
      $userRole = Role::firstOrCreate(['name' => 'user']);
      $user->assignRole($userRole);
    }

    return $user;
  }
}
