<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class CustomerUpdateRequest extends FormRequest
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
        $customer = Customer::findOrFail(request('id'));
        $customerId = $customer->id;

        return [
            'name' => 'required|string|min:4|max:100',
            'address' => 'nullable|string|max:100',
            'phone' => "nullable|unique:customers,phone,$customerId|min:9|max:13",
        ];
    }
}
