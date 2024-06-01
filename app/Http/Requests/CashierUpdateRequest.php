<?php

namespace App\Http\Requests;

use App\Models\Shop;
use App\Models\Cashier;
use Illuminate\Validation\Rule;
use App\Enums\GeneralStatusEnum;
use App\Helpers\Enum;
use Illuminate\Foundation\Http\FormRequest;

class CashierUpdateRequest extends FormRequest
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
        $cashier = Cashier::findOrFail(request('id'));
        $cashierId = $cashier->id;
        $enum = implode(',', (new Enum(GeneralStatusEnum::class))->values());

        return [
            'name'=>'required| string',
            'phone' => "nullable|unique:cashiers,phone,$cashierId|min:9|max:13",
            'address' => 'string| nullable| max:1000',
            'shop_id' => "required| in:$shops",
            'status' => "required| in:$enum"
        ];
    }
}
