<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
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
        'type'          => 'sometimes|string|in:mobile_phone,home_phone',
        'country_code'  => 'sometimes|string|min:2|max:4',
        'area_code'     => 'sometimes|string|max:4',
        'number'        => 'sometimes|string|min:8|max:9',
      ];
    }
}
