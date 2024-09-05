<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
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
          'name' => 'required|string',
          'number' => 'required|string',
          'description' => 'required|string',
          'price_per_minute' => 'required|numeric',
          'operating_hours' => 'nullable|string'
        ];
    }
}
