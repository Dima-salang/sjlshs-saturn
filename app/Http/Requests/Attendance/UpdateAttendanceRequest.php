<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
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
            'lrn' => ['sometimes', 'required', 'string', 'exists:students,lrn'],
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'grade_level' => ['sometimes', 'required', 'string', 'max:2'],
            'section_id' => ['sometimes', 'required', 'exists:sections,section_id'],
            'is_absent' => ['sometimes', 'required', 'boolean'],
            'is_late' => ['sometimes', 'required', 'boolean'],
            'scan_timestamp' => ['sometimes', 'required', 'date'],
        ];
    }
}
