<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class BulkStoreStudentRequest extends FormRequest
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
            'students' => 'required|array',
            'students.*.lrn' => 'required|string|max:12|unique:students,lrn',
            'students.*.first_name' => 'required|string|max:255',
            'students.*.last_name' => 'required|string|max:255',
            'students.*.middle_name' => 'nullable|string|max:255',
            'students.*.section_id' => 'required|exists:sections,section_id',
            'students.*.gender' => 'required|string|max:255',
            'students.*.grade_level' => 'required|string|max:2',
            'students.*.adviser_id' => 'nullable|exists:teachers,id',
        ];
    }
}
