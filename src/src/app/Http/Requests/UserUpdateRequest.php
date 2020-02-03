<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'ensurance_id' => 'required',
            'last_name' => 'required',
            'city' => 'required',
            'state' => 'required',
            'address' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required'
        ];
    }
}
