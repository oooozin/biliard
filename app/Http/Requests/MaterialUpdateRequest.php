<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\GeneralStatusEnum;
use App\Helpers\Enum;
use App\Models\Material;

class MaterialUpdateRequest extends FormRequest
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
        $material = Material::findOrFail(request('id'));
        $materialId = $material->id;
        $enum = implode(',', (new Enum(GeneralStatusEnum::class))->values());

        return [
            'name' => "required|string|unique:materials,$materialId",
            'status' => "required|in:$enum"
        ];
    }
}
