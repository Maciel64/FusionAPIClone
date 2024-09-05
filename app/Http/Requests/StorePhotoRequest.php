<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhotoRequest extends FormRequest
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
        'type'  => 'required|string|in:coworking,room,avatar,room_main_photo,coworking_main_photo',
        'uuid'  => 'required|string|uuid',
        'photo' => 'required|image|mimes:jpg,png,jpeg,webp|max:10240',
      ];
    }
}
