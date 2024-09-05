<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
      return true;
    }

    public function rules()
    {
      return [
        'name'     => 'sometimes|string|max:255',
        'phone'    => 'sometimes|string|max:255',
        'birth_date' => 'sometimes|date',
        'email'    => 'sometimes|string|email|max:255|unique:users',
        'password' => 'sometimes|string|min:8|confirmed',
      ];
    }
}
