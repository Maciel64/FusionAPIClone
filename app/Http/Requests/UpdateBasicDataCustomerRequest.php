<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBasicDataCustomerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $user = auth()->user();
        return [
            'name'     => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255',
            'email' => Rule::unique('users')->ignore($user->uuid, 'uuid'),
            'description' => 'sometimes|string|nullable'
        ];
    }
}
