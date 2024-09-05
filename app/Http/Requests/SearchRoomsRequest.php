<?php

namespace App\Http\Requests;

use App\Repositories\CategoryRepository;
use App\Repositories\CoworkingRepository;
use Illuminate\Foundation\Http\FormRequest;

class SearchRoomsRequest extends FormRequest
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
          'type' => 'required|string|in:city,coworking,category',
          'value' => 'required|string',
        ];
    }

    public function withValidator($validator)
    {
      switch ($this->type) {
        case 'coworking':
          $validator->after(function ($validator) {
            $coworking = new CoworkingRepository();
            if(!$coworking->findByUuid($this->value))
                $validator->errors()->add('value', 'Coworking not found');
          });
          break;
        case 'category':
          $validator->after(function ($validator) {
            $category = new CategoryRepository();
            if(!$category->findByUuid($this->value))
                $validator->errors()->add('value', 'Category not found');
          });
          break;
      }
    }
}
