<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBasicDataPartnerRequest extends FormRequest
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
        $user = auth()->user();
        return [
            'name'     => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255',
            'email' => Rule::unique('users')->ignore($user->uuid, 'uuid'),
        ];
    }
}
