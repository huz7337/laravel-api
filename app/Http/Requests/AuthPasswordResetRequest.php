<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthPasswordResetRequest extends FormRequest
{

    static private array $_passwordRules = ['required', 'min:6', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'];

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
            'token' => ['required'],
            'password' => ['confirmed', ...self::$_passwordRules],
            'email' => ['required', 'email:rfc,dns,filter']
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
            'password.min' => 'Your password must be at least 6 characters in length',
            'password.regex' => 'Your password must contain at least one digit, one uppercase letter and one symbol'
        ];
    }
}
