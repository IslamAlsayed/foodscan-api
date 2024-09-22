<?php

namespace App\Http\Requests\Items;

use Illuminate\Foundation\Http\FormRequest;

class ItemUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0.01|max:999999.99',
            'type' => 'string|in:vegetarian,non-vegetarian',
            'status' => 'string|in:1,0',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'exists:categories,id',
        ];
    }

    public function messages()
    {
        return [
            'category_id.exists' => 'The selected category id is invalid, or not exist',
        ];
    }
}
