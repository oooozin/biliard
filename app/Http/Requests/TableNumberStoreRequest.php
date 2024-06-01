<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Shop;
use App\Models\Cashier;
use App\Enums\TableStatusEnum;
use App\Helpers\Enum;

class TableNumberStoreRequest extends FormRequest
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
        $cashiers = Cashier::all()->pluck('id')->toArray();
        $cashiers = implode(',', $cashiers);
        $enum = implode(',', (new Enum(TableStatusEnum::class))->values());

        return [
            'name' => 'required| string',
            'description' => 'nullable| string',
            'amount' => 'required|numeric',
            'shop_id' => "required|in:$shops",
            'cashier_id' => "required|in:$cashiers"
        ];
    }
}
