<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCustomerRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZÀ-ú\s]+$/u',
            ],
            'email'                 => 'required|string|max:90|unique:users',
            'password'              => 'required|string|min:8',
            'password_confirmation' => 'required|string|min:8|same:password',
            'document'              => 'nullable|string|numeric|max_digits:20',
            'document_type'         => 'nullable|string|in:CPF,CNPJ,PASSPORT',
            'gender'                => 'nullable|string',
            'birth_date'            => 'nullable|date|before_or_equal:'.\Carbon\Carbon::now()->subYears(18)->format('d-m-Y'),
            'phones'                => 'required|array',
            // 'phones.home_phone'     => 'nullable|array',
            // 'phones.home_phone.country_code' => 'nullable|numeric',
            // 'phones.home_phone.area_code'    => 'nullable|numeric',
            // 'phones.home_phone.number'       => 'nullable|numeric',
            'phones.mobile_phone'             => 'required|array',
            'phones.mobile_phone.country_code' => 'required|numeric',
            'phones.mobile_phone.area_code'    => 'required|numeric',
            'phones.mobile_phone.number'       => 'required|numeric',
            'health_advice' => 'required|string|max:255|exists:health_advice,initials',
            'advice_code'   => 'required|string|max:255',
            'address'       => 'nullable|array',
            'address.line_1' => [
                'nullable',
                'string',
                'max:255'
            ],
            'address.line_2' => [
                'nullable',
                'string',
                'max:180'
            ],
            'address.city' => [
                'nullable',
                'string',
                'max:180',
                'regex:/^[a-zA-ZÀ-ú\s]+$/u',
            ],
            'address.state' => 'nullable|string|max:2|alpha:ascii',
            'address.country' => 'nullable|string|in:BR',
            'address.zip_code' => 'nullable|string',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nome',
            'document' => 'documento',
            'document_type' => 'tipo de documento',
            'gender' => 'gênero',
            'birth_date' => 'data de nascimento',
            'phones.mobile_phone.area_code' => 'DDD',
            'phones.mobile_phone.number' => 'número do telefone',
            'phones.mobile_phone.country_code' => 'código do país',
            'phones.home_phone.country_code' => 'código do país',
            'phones.home_phone.area_code' => 'DDD',
            'phones.home_phone.number' => 'número do telefone',
            'health_advice' => 'conselho de categoria',
            'advice_code' => 'número do conselho',
            'password' => 'senha',
            'password_confirmation' => 'confirmação de senha',
            'address.city' => 'cidade',
            'address.state' => 'estado',
            'address.zip_code' => 'CEP',
            'address.line_1' => 'logradouro',
            'address.line_2' => 'número',
            'address.country' => 'país',
        ];
    }
}
