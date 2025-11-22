<?php

namespace App\Http\Requests\API\User;

use Illuminate\Foundation\Http\FormRequest;

class LinkButtonRequest extends FormRequest
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
            'button_id' => 'required|exists:button_links,id',
            'order' => 'required|integer',
            'link.title' => 'required|string',
            'link.description' => 'nullable|string',
            'link.icon' => 'nullable|string',
            'link.link' => 'required|string',
        ];
    }
}
