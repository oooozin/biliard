<?php

namespace App\Http\Requests;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceStoreRequest extends FormRequest
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

        $items = Item::all()->pluck('id')->toArray();
        $items = implode(',', $items);

        $orders = Order::all()->pluck('id')->toArray();
        $orders = implode(',', $orders);

        return [
            'order_id' => ['required', "in:$orders"],
            'item_id' => ['required', "in:$items"],
            'qty' => 'nullable | numeric',
            'total' => 'required| numeric',
        ];
    }
}
