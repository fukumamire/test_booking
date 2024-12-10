<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\ShopController;

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

    // ヘッダーの標準化とマッピング
    $headerMapping = [
      '店舗名' => 'name',
      'ユーザーID' => 'user_id',
      '地域' => 'area_name',
      'ジャンル' => 'genres',
      '店舗概要' => 'outline',
      '画像URL' => 'image_url',
    ];

    $header = array_map(function ($value) use ($headerMapping) {
      return $headerMapping[$value] ?? null;
    }, $header);

    Log::info('Processed header:', $header);

    // 必須のヘッダーを定義
    $requiredHeaders = ['name', 'user_id', 'area_name', 'genres', 'outline', 'image_url'];

    // ヘッダーが不足している場合にエラーをスロー
    if (array_diff($requiredHeaders, $header)) {
      $missingHeaders = array_diff($requiredHeaders, $header);
      $errorMessage = "CSVファイルのヘッダーが不正です。以下のヘッダーが不足しています: " . implode(', ', $missingHeaders);
      $this->failedValidation(Validator::make([], []), new \Illuminate\Validation\ValidationException(
        Validator::make([], []),
        $errorMessage
      ));

      Log::error($errorMessage . " Required Headers: " . json_encode($requiredHeaders) . ", Actual Headers: " . json_encode($header));
    }

    // データ行を検証
    $dataRows = array_slice($rows, 1); // データ行のみ
    foreach ($dataRows as $index => $row) {
      $lineNumber = $index + 2; // データ行の行番号（+1 ヘッダー分）

      // ヘッダーとデータ行の列数が一致しているか確認
      if (
        count($header) !== count($row)
      ) {
        $errorMessage = "CSVの{$lineNumber}行目にデータ列の不足または余剰があります。";
        $this->failedValidation(Validator::make([], []), new \Illuminate\Validation\ValidationException(
          Validator::make([], []),
          $errorMessage
        ));

        // エラーログの記録
        Log::error($errorMessage . " Header count: " . count($header) . ", Row count: " . count($row));
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
      'outline' => null,
      'image_url' => null,
    ], $data);

    $validator = Validator::make($data, [
      'name' => 'required|string|max:50',
      'user_id' => 'required|integer|exists:users,id', // user_id必須 & データベースに存在確認
      'area_name' => [
        'required',
        'string',
        function (
          $attribute,
          $value,
          $fail
        ) use ($data) {
          if (!isset(ShopController::$DEFINED_AREAS[$value])) {
            $fail('area_nameは「東京」、「東京都」、「大阪」、「大阪府」、「福岡」、または「福岡県」のいずれかでなければなりません。');
          }
          $data['area_name'] = ShopController::$DEFINED_AREAS[$value];
        },
      ],
      'genres' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          $genres = explode(',', $value);
          foreach ($genres as $genre) {
            $genre = trim($genre);
            if (!in_array($genre, ['寿司', '焼肉', 'イタリアン', '居酒屋', 'ラーメン'])) {
              $fail('genresには「寿司」、「焼肉」、「イタリアン」、「居酒屋」、または「ラーメン」のいずれかを指定してください。複数のジャンルを指定する場合はカンマで区切ってください。');
            }
          }
        },
      ],
      'outline' => 'string|max:400', // 店舗概要の制約を追加
      'image_url' => [
        'required',
        'url',
        function ($attribute, $value, $fail) {
          if (!preg_match('/\.(jpeg|jpg|png)$/', $value)) {
            $fail('image_urlはJPEG、JPG、またはPNG形式でなければなりません。');
          }
        },
      ],
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
