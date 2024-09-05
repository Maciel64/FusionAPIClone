<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
      return [
        'uuid'         => 'required|string|uuid',
        'type'         => 'required|string|in:coworking,user,patient',
        'line_1'       => [
          'required',
          'string',
          'max:255'
      ],
        'line_2'       => [
          'sometimes',
          'string',
          'max:180'
      ],
        'city'         => [
          'required',
          'string',
          'max:180',
          'regex:/^[a-zA-ZÀ-ú\s]+$/u',
      ],
        'state'        => 'required|string|max:2',
        'country'      => 'required|string|max:255',
        'neighborhood' => 'required|string|max:255',
        'zip_code'     => 'required|string|max:10',
      ];
    }
}
