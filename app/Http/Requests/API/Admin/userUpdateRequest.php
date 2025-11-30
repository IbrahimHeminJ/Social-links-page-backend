<?php

namespace App\Http\Requests\API\Admin;

use Illuminate\Foundation\Http\FormRequest;

class userUpdateRequest extends FormRequest
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
        $userId = $this->route('id');
        
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $userId . ',id',
            'email' => 'required|email|unique:users,email,' . $userId . ',id',
            'phone_no' => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
            'password' => 'nullable|confirmed|string|min:8',
        ];
    }
}
