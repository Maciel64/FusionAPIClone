<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetFixedRoomRequest extends FormRequest
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'fixed' => $this->input('fixed', '0'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'room_uuid' => 'required|string|uuid',
            'fixed' => 'sometimes|boolean'
        ];
    }
}
