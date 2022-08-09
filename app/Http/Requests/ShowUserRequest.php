<?php

namespace App\Http\Requests;

use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ShowUserRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /**
         * @var User $userToEdit
         */
        $userToEdit = $this->route('user');

        // if we're trying to view someone else's profile, we must have a user
        if (!$userToEdit) {
            return false;
        }

        // the user is viewing their own profile, but based on ID
        if ($userToEdit->id == $this->user()->id) {
            return true;
        }

        // the user to be viewed is an athlete and the auth user has permissions to view athletes
        if ($userToEdit->hasRole(User::ROLE_ATHLETE) && $this->user()->can('view athlete')) {
            return true;
        }

        // the user to be viewed is an influencer and the auth user has permissions to view influencer
        if ($userToEdit->hasRole(User::ROLE_INFLUENCER) && $this->user()->can('view influencer')) {
            return true;
        }

        // the user to be viewed is a coach and the auth user has permissions to view coaches
        if ($userToEdit->hasRole(User::ROLE_COACH) && $this->user()->can('view coach')) {
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
        return [];
    }
}
