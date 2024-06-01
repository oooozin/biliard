<?php

namespace App\Http\Requests;

use App\Enums\OrderItemStatusEnum;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderItemUpdateRequest extends FormRequest
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

        $users = User::all()->pluck('id')->toArray();
        $users = implode(',', $users);

        $products = Product::all()->pluck('id')->toArray();
        $products = implode(',', $products);

        return [
            'order_id' => "in:$orders",
            'user_id' => "in:$users",
            'product_id' => "in:$products",
            'qty' => 'numeric|min:1|max:30',
            'status' => Rule::in([
                OrderItemStatusEnum::SELECTED->value,
                OrderItemStatusEnum::ORDERED->value,
                OrderItemStatusEnum::SUCCESS->value,
            ]),
            'total_price' => 'numeric',
            'profit' => 'numeric',
            'ordered_at' => 'date_format:Y-m-d H:i:s',
        ];
    }
}
