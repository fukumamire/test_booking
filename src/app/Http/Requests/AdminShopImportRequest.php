<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Auth;

class AdminShopImportRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    // ユーザーが認証済みかつ管理者権限を持っているかチェック
    return Auth::check() && $this->user()->isAdmin();
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'file' => 'required|mimes:csv,txt|max:51200',
    ];
  }
  public function messages()
  {
    return [
      'file.required' => 'ファイルを選択してください。',
      'file.mimes' => '有効なファイルタイプは csv, txt のみです。',
      'file.max' => 'ファイルサイズは最大 50MB までです。',
    ];
  }
}
