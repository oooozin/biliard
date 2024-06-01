<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\GeneralStatusEnum;
use App\Helpers\Enum;

class MaterialStoreRequest extends FormRequest
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
        $enum = implode(',', (new Enum(GeneralStatusEnum::class))->values());

        return [
            'name' => 'required| string|unique:materials,name',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'status' => "required|in:$enum"
        ];
    }
}
