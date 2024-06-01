<?php

namespace App\Http\Requests;

use App\Models\Shop;
use App\Models\Material;
use Illuminate\Foundation\Http\FormRequest;

class TransferMaterialUpdateRequest extends FormRequest
{
    /**
     * Determine if the TransferMaterial is authorized to make this request.
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
        $shops = Shop::all()->pluck('id')->toArray();
        $shops = implode(',', $shops);
        $material = Material::all()->pluck('id')->toArray();
        $material = implode(',', $material);
  
        return [
            'from_shop' => "required|in:$shops",
            'to_shop' => "required|in:$shops",
            'item_id' => "nullable|in:$material",
            'qty' => 'required|numeric',
        ];
    }
}
