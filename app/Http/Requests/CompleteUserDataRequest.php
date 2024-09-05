<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteUserDataRequest extends FormRequest
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
        'user_uuid' => 'required|string',
        'name' => [
            'nullable',
            'string',
            'max:255',
            'regex:/^[a-zA-ZÀ-ú\s]+$/u',
        ],
        'document'              => 'required|string|numeric|max_digits:20',
        'document_type'         => 'required|string|in:CPF,CNPJ,PASSPORT',
        'gender'                => 'nullable|string',
        'birth_date' => 'required|date|before_or_equal:'.\Carbon\Carbon::now()->subYears(18)->format('d-m-Y'),

        'phones'                => 'required|array',

        'phones.home_phone'              => 'sometimes|array',
        'phones.home_phone.country_code' => 'sometimes|string',
        'phones.home_phone.area_code'    => 'sometimes|string',
        'phones.home_phone.number'       => 'sometimes|string',

        'phones.mobile_phone'              => 'sometimes|array',
        'phones.mobile_phone.country_code' => 'sometimes|string',
        'phones.mobile_phone.area_code'    => 'sometimes|string',
        'phones.mobile_phone.number'       => 'sometimes|string',


        'address'          => 'required|array',
        'address.line_1'       => [
          'required',
          'string',
          'max:255'
      ],
        'address.line_2'       => [
          'nullable',
          'string',
          'max:180'
      ],
      'address.neighborhood' => 'nullable|string|max:255',
        'address.city'         => [
          'required',
          'string',
          'max:180',
          'regex:/^[a-zA-ZÀ-ú\s]+$/u',
      ],
        'address.state'        => 'required|string|max:2|alpha:ascii',
        'address.country'  => 'required|string|in:BR',
        'address.zip_code' => 'required|string',
        
        "card" => "required|array",
        'card.number'                       => 'required|string',
        'card.holder_name'                  => 'required|string',
        'card.holder_document'              => 'required|string',
        'card.exp_month'                    => 'required|string',
        'card.exp_year'                     => 'required|string',
        'card.cvv'                          => 'required|string',
        'card.brand'                        => 'required|string|in:elo,mastercard,visa,amex,jcb,aura,hipercard,diners,discover,allele,vr,sodexo',
        'card.billing_address_is_different' => 'sometimes|boolean',
        'card.billing_address'              => 'sometimes|array',
        'card.billing_address.line_1'       => 'sometimes|string',
        'card.billing_address.line_2'       => 'nullable|string',
        'card.billing_address.city'         => 'sometimes|string',
        'card.billing_address.state'        => 'sometimes|string',
        'card.billing_address.country'      => 'sometimes|string|in:BR',
        'card.billing_address.zip_code'     => 'sometimes|string',
        'card.is_default'                   => 'required|boolean'
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
          'address.city' => 'cidade',
          'address.state' => 'estado',
          'address.zip_code' => 'CEP',
          'address.line_1' => 'logradouro',
          'address.line_2' => 'número',
          'address.country' => 'país',

          'card.number' => 'número do cartão',
          'card.holder_name' => 'nome do titular do cartão',
          'card.holder_document' => 'documento do titular do cartão',
          'card.exp_month' => 'mês de expiração do cartão',
          'card.exp_year' => 'ano de expiração do cartão',
          'card.cvv' => 'CVV do cartão',
          'card.brand' => 'bandeira do cartão',
          'card.billing_address_is_different' => 'endereço de cobrança é diferente',
          'card.billing_address.line_1' => 'logradouro do endereço de cobrança',
          'card.billing_address.line_2' => 'número do endereço de cobrança',
          'card.billing_address.city' => 'cidade do endereço de cobrança',
          'card.billing_address.state' => 'estado do endereço de cobrança',
          'card.billing_address.country' => 'país do endereço de cobrança',
          'card.billing_address.zip_code' => 'CEP do endereço de cobrança',
      ];
    }
}
