<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can(User::PERMISSION_POST_UPDATE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'unique:posts,title,' . $this->route('post')->id],
            'content' => ['required'],
            'slug' => ['unique:posts,slug,' . $this->route('post')->id],
            'active' => ['boolean'],
            'category_id' => ['integer', Rule::exists('categories', 'id')]
        ];
    }
}
