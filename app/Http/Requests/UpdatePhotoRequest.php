<?php

namespace App\Http\Requests;

use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdatePhotoRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // the user is updating their own profile
        if ($this->route()->uri === 'api/me/photo') {
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

        // the user to be edited is an athlete and the auth user has permissions to edit athletes
        if ($userToEdit->hasRole(User::ROLE_ATHLETE) && $this->user()->can('update athlete')) {
            return true;
        }

        // the user to be edited is a coach and the auth user has permissions to edit coaches
        if ($userToEdit->hasRole(User::ROLE_COACH) && $this->user()->can('update coach')) {
            return true;
        }

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
            'photo' => ['required', 'file']
        ];
    }
}
