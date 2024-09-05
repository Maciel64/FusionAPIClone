<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class IndexOrderedAppointmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
          'dateInit' => 'nullable|date',
          'dateEnd' => 'nullable|date',
          'ordered_attribute' => 'string|in:created_at,time_init',
          'orientation' => 'string|in:asc,desc',
          'status' => 'nullable|string',
          'coworking_uuid' => 'nullable|string',
          'room_uuid' => 'nullable|string', 
          'customer_uuid' => 'nullable|string'
        ];
    }


}
