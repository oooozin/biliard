<?php

namespace App\Http\Requests;

use App\Models\Shop;
use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;

class TransferItemUpdateRequest extends FormRequest
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
        $shops = Shop::all()->pluck('id')->toArray();
        $shops = implode(',', $shops);
        $item = Item::all()->pluck('id')->toArray();
        $item = implode(',', $item);
  
        return [
            'from_shop' => "required|in:$shops",
            'to_shop' => "required|in:$shops",
            'item_id' => "nullable|in:$item",
            'qty' => 'required|numeric',
        ];
    }
}
