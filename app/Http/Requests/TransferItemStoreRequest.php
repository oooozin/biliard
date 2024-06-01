<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Shop;
use App\Models\Item;
use App\Models\ItemData;

class TransferItemStoreRequest extends FormRequest
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
            'to_shop' => "required|in:$shops|different:from_shop",
            'item_id' => "nullable|in:$item",
            'qty' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $itemData = ItemData::where('item_id', $this->input('item_id'))
                    ->where('shop_id', $this->input('from_shop'))
                    ->first();
                    if ($itemData && $value > $itemData->qty) {
                        $fail("The qty must be less than $itemData->qty");
                    }elseif($itemData == ""){
                        $fail("The qty must be less than 0");
                    }
                },
            ],
        ];
    }
}
