<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class OrderItemStoreRequest extends FormRequest
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
        $orders = Order::all()->pluck('id')->toArray();
        $orders = implode(',', $orders);

        $products = Product::all()->pluck('id')->toArray();
        $products = implode(',', $products);

        return [
            'order_id' => "required| in:$orders",
            'product_id' => "required| in:$products",
            'qty' => 'required| numeric |min:1|max:30',
            'total_price' => 'required| numeric',
            'profit' => 'required| numeric',
        ];
    }
}
