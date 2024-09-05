<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeactiveOrActiveUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->role_name == 'admin' || auth()->user()->role_name == 'owner';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_uuid' => 'required|uuid|exists:users,uuid',
            'setStatus' => 'required|boolean'
        ];
    }
}
