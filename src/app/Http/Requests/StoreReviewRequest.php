<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'rating' => 'required|integer|min:1|max:5',
			'comment' => 'required|string|min:20|max:400',
			'shop_id' => 'required|exists:shops,id',
			'image_url' => 'nullable|image|mimes:jpeg,png|max:2048',
		];
	}

	public function messages()
	{
		return [
			'rating.required' => '評価は必須です。',
			'comment.min' => 'コメントは20文字以上でなければなりません。',
			'image_url.image' => '画像ファイルのみアップロードできます。',
			'image_url.mimes' => 'JPEGまたはPNG形式の画像のみアップロードできます。',
			'image_url.max' => '画像のサイズは2MB以下にしてください。',
		];
	}
}
