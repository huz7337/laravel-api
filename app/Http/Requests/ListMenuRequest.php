<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Queries\MenuQuery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ListMenuRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only providers can access this resource
        if (Auth::user()->can(User::PERMISSION_MENU_LIST)) {
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
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort_column' => ['string', Rule::in(array_keys(MenuQuery::$sort))],
            'sort_direction' => ['string', Rule::in('asc', 'desc')],
        ];
    }
}
