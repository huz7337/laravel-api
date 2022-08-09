<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthLoginRequest extends FormRequest
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
            'email' => ['required', 'email:rfc,dns,filter', Rule::exists('users', 'email')],
            'password' => ['required']
        ];
    }

    /**
     * Get the custom messages in case of errors
     *
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'email.exists' => __('Invalid credentials'),
        ];
    }
}
