<?php

namespace App\Http\Requests\DiningTables;

use Illuminate\Foundation\Http\FormRequest;

class DiningTableStoreRequest extends FormRequest
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
            'floor' => 'required|min:1|max:255',
            'size' => 'required|min:1|max:255',
            'status' => 'string|in:1,0',
        ];
    }
}
