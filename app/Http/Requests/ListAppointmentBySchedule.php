<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListAppointmentBySchedule extends FormRequest
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
          'schedule_uuid' => 'required|uuid|exists:schedules,uuid',
          'data_init'     => 'required|date',
          'data_end'      => 'required|date',
        ];
    }
}
