<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateAddressRequest extends FormRequest
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
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        return [
          'line_1'   => 'sometimes|string|max:180',
          'line_2'   => 'sometimes|string|max:180',
          'city'     => 'sometimes|string|max:180',
          'state'    => 'sometimes|string|max:2',
          'country'  => 'sometimes|string|max:255',
          'zip_code' => 'sometimes|string|max:10',
          'neighborhood' => 'sometimes|string|max:255',
        ];
    }
}
