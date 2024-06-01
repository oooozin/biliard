<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Enums\GeneralStatusEnum;
use App\Helpers\Enum;
use Illuminate\Foundation\Http\FormRequest;

class ItemStoreRequest extends FormRequest
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
        $categories = Category::all()->pluck('id')->toArray();
        $categories = implode(',', $categories);
        $enum = implode(',', (new Enum(GeneralStatusEnum::class))->values());

        return [
            'name' => 'required| string|unique:items,name',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'price' => 'required| numeric',
            'barcode' => 'nullable| numeric',
            'purchase_price' => 'required| numeric',
            'category_id' => "required| in:$categories",
            'status' => "required|in:$enum"
        ];
    }
}
