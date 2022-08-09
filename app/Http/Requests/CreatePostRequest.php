<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePostRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can(User::PERMISSION_POST_CREATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'unique:posts,title'],
            'content' => ['required'],
            'slug' => ['max:100'],
            'active' => ['boolean'],
            'category_id' => ['integer', Rule::exists('categories', 'id')]
        ];
    }
}
