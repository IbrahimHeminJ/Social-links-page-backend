<?php

namespace App\Http\Requests\API\Admin;

use App\Models\Report;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
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
            'report_status' => 'required|boolean',
            'handled_by' => 'required|exists:users,id',
            'reason_of_action' => 'required|string',
        ];
    }
}
