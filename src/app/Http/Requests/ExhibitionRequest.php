<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            //
            'name' => 'required|string',
            'description'=> 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|string',
            'condition_id' => 'required|integer',
            'item_path' => 'required|file|mimes:jpeg,png',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名は必須です',
            'description.required' => '商品説明は必須です',
            'description.max' => '商品説明は255文字以内で入力してください',
            'item_path.required' => '商品画像は必須です',
            'item_path.mimes' => '商品画像はjpegかpng形式でアップロードしてください',
            'category_id.required' => 'カテゴリーは必須です',
            'condition_id.required' => '商品の状態は必須です',
            'price.required' => '商品価格は必須です',
            'price.numeric' => '商品価格は数字で入力してください',
            'price.min' => '商品価格は0円以上で入力してください',
        ];
    }
}
