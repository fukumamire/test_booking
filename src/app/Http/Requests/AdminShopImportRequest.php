<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


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
      'file' => 'required|mimes:csv,txt|max:51200', // ファイルタイプとサイズ
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

  /**
   * バリデーション後のカスタム検証.
   *
   * @return void
   */
  public function passedValidation()
  {
    $file = $this->file('file');
    $rows = array_map('str_getcsv', file($file->getRealPath()));
    $header = array_map('trim', $rows[0] ?? []);


    // ヘッダーの標準化
    $header = array_map(function ($value) {
      $value = str_replace('ID', 'ユーザーID', $value);
      $value = str_replace('URL', 'URL', $value);
      return $value;
    }, $header);


    $header = array_map(function ($value) {
      return mb_convert_kana(trim($value), 'as'); // トリム + 全角/半角変換  ヘッダーを正規化
    }, $header);

    // 必須のヘッダーを定義
    // $requiredHeaders = ['店舗名', ['ユーザーID', 'ユーザーＩＤ'], '地域', 'ジャンル', '店舗概要', ['画像URL', '画像ＵＲＬ']];
    $requiredHeaders = ['店舗名', 'ユーザーID', '地域', 'ジャンル', '店舗概要', '画像URL'];

    // ヘッダーが不足している場合にエラーをスロー
    if (array_diff($requiredHeaders, $header)) {
      abort(422, 'CSVファイルのヘッダーが不正です: ' . implode(', ', $requiredHeaders));
    }


    // データ行を検証
    $dataRows = array_slice($rows, 1);
    foreach ($dataRows as $index => $row) {
      $data = array_combine($header, $row);
      $this->validateRow($data, $index + 2); // データ行番号 +1 (ヘッダー分)
    }
  }

  /**
   * 各行をバリデーション.
   *
   * @param array $data
   * @param int $lineNumber
   * @return void
   */
  private function validateRow(array $data, int $lineNumber)
  {
    $validator =
      Validator::make($data, [
        'name' => 'required|string|max:50',
        'user_id' => 'required|integer|exists:users,id',
        'area_name' => 'required|string|in:東京都,大阪府,福岡県', // 許可されたエリアを指定
        'genres' => 'required|string|in:寿司,焼肉,イタリアン,居酒屋,ラーメン', // 許可されたジャンル
        'image_url' => 'required|url|regex:/\.(jpeg|png)$/i',
      ]);

    if ($validator->fails()) {
      abort(422, "CSVの{$lineNumber}行目にエラーがあります: " . implode(', ', $validator->errors()->all()));
    }
  }
}
