<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Http\JsonResponse;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Student>
     */
    public function index(): \Illuminate\Database\Eloquent\Collection
    {
        return Student::all();
    }

    /**
     * Display the specified student.
     *
     * @param  int  $id
     * @return Student
     */
    public function show(int $id): Student
    {
        return Student::findOrFail($id);
    }


    /**
     * Update the specified student.
     *
     * @param  int  $id
     * @return Student
     */
    public function update(Request $request, int $id): Student
    {
        $validated = $request->validate([
            'lrn' => 'required|string|max:11',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'grade_level' => 'required|string|max:2',
            'section_id' => 'required|exists:sections,section_id',
            'adviser_id' => 'required|exists:teachers,id',
        ]);

        $student = Student::findOrFail($id);
        $student->update($validated);

        return $student;
    }

    /**
     * Remove the specified student.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully',
        ], 200);
    }
}
