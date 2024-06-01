<?php

namespace App\Http\Requests;

use App\Enums\GeneralStatusEnum;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\Enum;

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
        $categories = Category::all()->pluck('id')->toArray();
        $categories = implode(',', $categories);
        $item = Item::findOrFail(request('id'));
        $itemId = $item->id;
        $enum = implode(',', (new Enum(GeneralStatusEnum::class))->values());

        return [
            'name' => "required|string|unique:items,name,$itemId",
            'price' => 'required|numeric',
            'barcode' => 'nullable| numeric',
            'purchase_price' => 'required|numeric',
            'status' => "required|in:$enum",
            'category_id' => "required|in:$categories",
        ];
    }
}
