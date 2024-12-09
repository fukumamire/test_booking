<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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

    Log::info('Raw header:', $header);

    // ヘッダーの標準化
    $header = array_map(function ($value) {
      $value = str_replace('ユーザーID', 'ユーザーID', $value);
      $value = str_replace('画像URL', '画像URL', $value);
      return mb_convert_kana(trim($value), 'as'); // トリム + 全角/半角変換
    }, $header);

    Log::info('Processed header:', $header);

    // 必須のヘッダーを定義
    $requiredHeaders = ['店舗名', 'ユーザーID', '地域', 'ジャンル', '店舗概要', '画像URL'];

    // ヘッダーが不足している場合にエラーをスロー
    if (array_diff($requiredHeaders, $header)) {
      Log::info('Header difference:', array_diff($requiredHeaders, $header));
      abort(422, 'CSVファイルのヘッダーが不正です: ' . implode(', ', $requiredHeaders));
    }

    // データ行を検証
    $dataRows = array_slice($rows, 1); // データ行のみ
    foreach ($dataRows as $index => $row) {
      $lineNumber = $index + 2; // データ行の行番号（+1 ヘッダー分）

      // ヘッダーとデータ行の列数が一致しているか確認
      if (count($header) !== count($row)) {
        abort(422, "CSVの{$lineNumber}行目にデータ列の不足または余剰があります。");
      }

      $data = array_combine($header, $row);
      $this->validateRow($data, $lineNumber); // データ行のバリデーション
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
    // 入力データに不足があればデフォルト値で補完
    $data = array_merge([
      'name' => null,
      'user_id' => 1, // デフォルトで1
      'area_name' => null,
      'genres' => null,
      'image_url' => null,
    ], $data);

    $validator = Validator::make($data, [
      'name' => 'required|string|max:50',
      'user_id' => 'required|integer|exists:users,id', // user_id必須 & データベースに存在確認
      'area_name' => 'required|string|in:東京都,大阪府,福岡県', // 許可されたエリアを指定
      'genres' => 'required|string|in:寿司,焼肉,イタリアン,居酒屋,ラーメン', // 許可されたジャンル
      'image_url' => 'required|url|regex:/\.(jpeg|jpg|png)$/i', // jpgも許可
    ]);

    if ($validator->fails()) {
      // エラー情報を詳細に表示
      $errors = [];
      foreach ($validator->errors()->toArray() as $field => $messages) {
        $errors[] = "{$field}: " . implode(', ', $messages);
      }
      abort(422, "CSVの{$lineNumber}行目にエラーがあります: " . implode('; ', $errors));
    }
  }
}
