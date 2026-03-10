<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(): \Illuminate\Database\Eloquent\Collection
    {
        // get the user
        $user = auth()->user();

        // if the user is admin, return all students
        if ($user->is_admin) {
            return Student::all();
        }

        // if the user is a teacher, return only the students in their section
        if ($user->teacher) {
            return Student::where('section_id', $user->teacher->section_id)->get();
        }

        // otherwise, return an empty collection
        return collect();
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request): Student
    {
        $validated = $request->validate([
            'lrn' => 'required|string|max:12|unique:students,lrn',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'grade_level' => 'required|string|max:2',
            'section_id' => 'required|exists:sections,section_id',
            'adviser_id' => 'nullable|exists:teachers,id',
        ]);

        return Student::create($validated);
    }

    /**
     * Display the specified student.
     */
    public function show(string $lrn): Student
    {
        return Student::findOrFail($lrn);
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, string $lrn): Student
    {
        $validated = $request->validate([
            'lrn' => 'required|string|max:12|unique:students,lrn,'.$lrn.',lrn',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'grade_level' => 'required|string|max:2',
            'section_id' => 'required|exists:sections,section_id',
            'adviser_id' => 'nullable|exists:teachers,id',
        ]);

        $student = Student::findOrFail($lrn);
        $student->update($validated);

        return $student;
    }

    /**
     * Remove the specified student.
     */
    public function destroy(string $lrn): JsonResponse
    {
        $student = Student::findOrFail($lrn);
        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully',
        ], 200);
    }

    /**
     * Bulk store students.
     */
    public function bulkStore(\App\Http\Requests\Student\BulkStoreStudentRequest $request): JsonResponse
    {
        $students = $request->validated()['students'];

        foreach ($students as $studentData) {
            Student::create($studentData);
        }

        return response()->json([
            'message' => count($students).' students imported successfully',
        ], 201);
    }
}
