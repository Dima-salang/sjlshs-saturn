<?php

namespace App\Services;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Collection;

class TeacherService
{
    /**
     * Get all teachers.
     *
     * @return Collection<int, Teacher>
     */
    public function getAllTeachers(): Collection
    {
        return Teacher::with('user')->get();
    }

    /**
     * Get a specific teacher.
     */
    public function getTeacher(int $id): ?Teacher
    {
        return Teacher::with('user')->find($id);
    }

    /**
     * Update a teacher.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateTeacher(int $id, array $data): Teacher|string
    {
        $teacher = Teacher::with('user')->find($id);

        if (! $teacher) {
            return 'Teacher not found';
        }

        // Handle user-level flags
        $userData = array_intersect_key($data, array_flip(['is_active', 'is_admin', 'is_nonFaculty']));
        if (! empty($userData) && $teacher->user) {
            $teacher->user->update($userData);
        }

        // Filter out user-level flags for teacher model update
        $teacherData = array_diff_key($data, $userData);
        $teacher->update($teacherData);

        return $teacher->fresh('user');
    }

    /**
     * Delete a teacher (and the associated user).
     */
    public function deleteTeacher(int $id): bool|string
    {
        $teacher = Teacher::find($id);

        if (! $teacher) {
            return 'Teacher not found';
        }

        // Deleting the teacher will trigger user deletion if we want that,
        // or we just delete the user which triggers teacher deletion via Observer.
        if ($teacher->user) {
            return $teacher->user->delete();
        }

        return $teacher->delete();
    }
}
