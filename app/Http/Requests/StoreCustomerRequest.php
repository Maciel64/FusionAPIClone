<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
      return [
        'name'          => 'required|string|max:255',
        'email'         => 'required|string|max:90|unique:users',
        'document'      => 'required|string|max:20',
        'document_type' => 'required|string|in:CPF,CNPJ,PASSPORT',
        'gender'        => 'nullable|string',
        'birth_date'    => 'required|date',
        'phones'        => 'required|array',

        'phones.home_phone' => 'sometimes|array',
        'phones.home_phone.country_code' => 'sometimes|numeric',
        'phones.home_phone.area_code' => 'sometimes|numeric',
        'phones.home_phone.number' => 'sometimes|numeric',

        'phones.mobile_phone' => 'required|array',
        'phones.mobile_phone.country_code' => 'required|numeric',
        'phones.mobile_phone.area_code' => 'required|numeric',
        'phones.mobile_phone.number' => 'required|numeric',

        'health_advice' => 'required|string|max:255|exists:health_advice,initials',
        'advice_code'   => 'required|string|max:255',

        'address'              => 'required|array',
        'address.line_1'       => 'required|string',
        'address.line_2'       => 'sometimes|string',
        'address.city'         => 'required|string',
        'address.state'        => 'required|string',
        'address.country'      => 'required|string',
        'address.zip_code'     => 'required|string',
      ];
  }

}
