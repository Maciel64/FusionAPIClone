<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCoworkingOpeningHoursRequest extends FormRequest
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
        'settings'             => 'array|required',
        'settings.*.day_of_week' => 'required|string',
        'settings.*.opening'     => 'required|date_format:H:i',
        'settings.*.closing'     => 'required|date_format:H:i',
      ];
    }

    public function withValidator($validator)
    {
      $validator->after(function ($validator) {
        $settings = $this->input('settings');
        foreach ($settings as $setting) {
          if (strtotime($setting['opening']) > strtotime($setting['closing'])) {
            $validator->errors()->add('settings', 'Opening time must be before closing time in day_of_week ('.$setting['day_of_week'].')');
          }
        }
      });
    }
}
