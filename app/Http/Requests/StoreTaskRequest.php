<?php

namespace App\Http\Requests;

use http\Env\Request;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'bail|required|min:3|max:255|',
            'description' => 'bail|required|min:3|max:255',
            'date' => 'bail|required|date_format:Y-m-d|after_or_equal:' . date('Y-m-d'),
        ];
    }
}
