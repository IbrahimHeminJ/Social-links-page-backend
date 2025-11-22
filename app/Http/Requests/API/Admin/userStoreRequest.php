<?php

namespace App\Http\Requests\API\Admin;

use Illuminate\Foundation\Http\FormRequest;

class userStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'phone_no' => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
            'role' => 'required|string|in:admin,user',
            'is_deleted' => 'required|boolean',
        ];
    }
}
