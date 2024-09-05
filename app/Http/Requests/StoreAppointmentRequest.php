<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
          'customer_uuid' => 'required|exists:users,uuid',
          'patient_name'  => 'sometimes|string|max:255',
          'patient_phone' => 'sometimes|string|max:20',
          'room_uuid'     => 'required|exists:rooms,uuid',
          'time_init'     => 'required|date',
          'time_end'      => 'required|date'
        ];
    }
}
