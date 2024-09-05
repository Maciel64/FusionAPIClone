<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
          'number'                       => 'required|string',
          'holder_name'                  => 'required|string',
          'holder_document'              => 'required|string',
          'exp_month'                    => 'required|string',
          'exp_year'                     => 'required|string',
          'cvv'                          => 'required|string',
          'brand'                        => 'required|string|in:elo,mastercard,visa,amex,jcb,aura,hipercard,diners,discover,allele,vr,sodexo',
          'billing_address_is_different' => 'sometimes|boolean',
          'billing_address'              => 'sometimes|array',
          'billing_address.line_1'       => 'sometimes|string',
          'billing_address.line_2'       => 'sometimes|string',
          'billing_address.city'         => 'sometimes|string',
          'billing_address.state'        => 'sometimes|string',
          'billing_address.country'      => 'sometimes|string',
          'billing_address.zip_code'     => 'sometimes|string',
          'is_default'                   => 'required|boolean',
        ];
    }

    public function  withValidator($validator)
    {
      $validator->after(function ($validator) {
        if ($this->billing_address_is_different) {
          if(is_array($this->billing_address)){
            switch (true) {
              case !isset($this->billing_address['line_1']):
                $validator->errors()->add('billing_address.line_1', 'billing_address.line_1 is required');
              case !isset($this->billing_address['city']):
                $validator->errors()->add('billing_address.city', 'billing_address.city is required');
              case !isset($this->billing_address['state']):
                $validator->errors()->add('billing_address.state', 'billing_address.state is required');
              case !isset($this->billing_address['country']):
                $validator->errors()->add('billing_address.country', 'billing_address.country is required');
              case !isset($this->billing_address['zip_code']):
                $validator->errors()->add('billing_address.zip_code', 'billing_address.zip_code is required');
            }            
          }else{
            $validator->errors()->add('billing_address', 'billing_address is required');
          }
        }
      });
    }
}
