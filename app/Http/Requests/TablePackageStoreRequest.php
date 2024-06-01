<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TablePackageStoreRequest extends FormRequest
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
            'name' => 'required| string',
            'hour' => ['required', 'regex:/^(0?[1-9]|1[0-2]):[0-5][0-9]$/'],
            'price' => 'required| numeric',
        ];
    }
}
