<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillUpdateRequest extends FormRequest
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

        return [
            'name' => 'required|string',
            'amount' => 'required|numeric',
            'shop_id' => "required|in:$shops"
        ];
    }
}
