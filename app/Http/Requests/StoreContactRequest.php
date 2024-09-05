<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
        'resource_type' => 'required|in:user,coworking,patient',
        'resource_uuid' => 'required|string|uuid',
        'type'          => 'required|string|in:mobile_phone,home_phone',
        'country_code'  => 'required|string|min:2|max:4',
        'area_code'     => 'required|string|max:4',
        'number'        => 'required|string|min:8|max:9',
      ];
    }
}
