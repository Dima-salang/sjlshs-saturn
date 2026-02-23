<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Services\TeacherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeacherController extends Controller
{
    public function __construct(
        protected TeacherService $teacherService
    ) {}

    /**
     * Display a listing of teachers.
     */
    public function index(): AnonymousResourceCollection
    {
        return TeacherResource::collection($this->teacherService->getAllTeachers());
    }

    /**
     * Display the specified teacher.
     */
    public function show(int $id): TeacherResource|JsonResponse
    {
        $teacher = $this->teacherService->getTeacher($id);

        if (! $teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        return new TeacherResource($teacher);
    }

    /**
     * Update the specified teacher.
     */
    public function update(UpdateTeacherRequest $request, int $id): TeacherResource|JsonResponse
    {
        $result = $this->teacherService->updateTeacher($id, $request->validated());

        if (is_string($result)) {
            return response()->json(['message' => $result], 404);
        }

        return new TeacherResource($result);
    }

    /**
     * Remove the specified teacher.
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->teacherService->deleteTeacher($id);

        if ($result === true) {
            return response()->json(['message' => 'Teacher deleted successfully']);
        }

        return response()->json(['message' => is_string($result) ? $result : 'Failed to delete teacher'], 400);
    }
}
