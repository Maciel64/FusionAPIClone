<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
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
          'name' => 'sometimes|string',
          'number' => 'sometimes|string',
          'description' => 'sometimes|string',
          'price_per_minute' => 'sometimes|numeric',
          'appointment_restriction_hour' => 'nullable|integer',
          'appointment_type' => 'nullable'
        ];
    }
}
