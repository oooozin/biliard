<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Material;
use App\Models\Shop;

class MaterialDataStoreRequest extends FormRequest
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
        $material = Material::all()->pluck('id')->toArray();
        $material = implode(',', $material);

        $shop = Shop::all()->pluck('id')->toArray();
        $shop = implode(',', $shop);
        

        return [
            'qty' => 'required| numeric',
            'material_id' => "required| in:$material",
            'shop_id' => "required|in:$shop"
        ];
    }
}
