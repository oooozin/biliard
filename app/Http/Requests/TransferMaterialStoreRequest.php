<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Shop;
use App\Models\Material;
use App\Models\MaterialData;

class TransferMaterialStoreRequest extends FormRequest
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
            'to_shop' => "required|in:$shops|different:from_shop",
            'material_id' => "nullable|in:$material",
            'qty' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $materialData = MaterialData::where('material_id', $this->input('material_id'))
                    ->where('shop_id', $this->input('from_shop'))
                    ->first();
                    if ($materialData && $value > $materialData->qty) {
                        $fail("The qty must be less than $materialData->qty");
                    }elseif($materialData == ""){
                        $fail("The qty must be less than 0");
                    }
                },
            ],
        ];
    }
}
