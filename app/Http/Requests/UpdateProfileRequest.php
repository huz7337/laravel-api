<?php

namespace App\Http\Requests;

use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // the user is updating their own profile
        if ($this->route()->uri === 'api/me') {
            return true;
        }

        /**
         * @var User $userToEdit
         */
        $userToEdit = $this->route('user');

        // if we're trying to edit someone else's profile, we must have a user
        if (!$userToEdit) {
            return false;
        }

        // the user is updating their own profile again, but based on ID
        if ($userToEdit->id == $this->user()->id) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['email', 'unique:users,email,' . Auth::user()->id],
            'first_name' => ['string', 'max:25'],
            'last_name' => ['string', 'max:25'],
            'date_of_birth' => ['nullable', 'date', 'before:' . Carbon::now()->subYears(14)->format('Y-m-d')],
            'profile' => ['nullable', 'array'],
            'profile.photo' => ['nullable', 'file'],
            'profile.description' => ['nullable', 'string', 'max:300'],
            'profile.phone_number' => ['nullable', 'string', 'min:10', 'max:12'],
            'profile.gender' => ['nullable', 'string', Rule::in(Profile::$genders)],
        ];
    }

    /**
     * Get the custom messages in case of errors
     *
     * @return array|string[]
     */
    public function messages(): array
    {
        return [
            'date_of_birth.before' => __('You must be at least 14 years old to have an account.'),
        ];
    }


    /**
     * Show custom names for attributes in errors
     *
     * @return array|string[]
     */
    public function attributes(): array
    {
        return [
            'profile.phone_number' => 'phone number',
            'profile.description' => 'description',
        ];
    }
}
