<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentBulkRequest extends FormRequest
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
        'customer_uuid'            => 'required|exists:users,uuid',
        'room_uuid'                => 'required|exists:rooms,uuid',
        'appointments'             => 'required|array',
        'appointments.*.time_init' => 'required|date',
        'appointments.*.time_end'  => 'required|date',
      ];
    }
}
