<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenericSearchRequest extends FormRequest
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
          'value' => 'nullable|string',
          'date' => 'nullable|date',
          'attributeToFilter' => 'nullable|string|in:created_at,price_per_minute',
          'attributeSortBy' => 'nullable|string|in:asc,desc',
        ];
    }

    
}
