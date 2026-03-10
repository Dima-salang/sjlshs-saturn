<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Student;

class AttendanceService
{
    public function addAttendanceRecord(array $decoded, bool $is_late = false): string
    {
        try {
            // get the student data from the param
            $lrn = $decoded['lrn'];

            // check if the student is already present
            $student = Student::where('lrn', $lrn)->first();
            if ($student) {
                $attendance = Attendance::where('lrn', $lrn)
                    ->whereDate('created_at', now()->today())
                    ->first();

                if ($attendance) {
                    return 'Student is already present today';
                }
                $attendance = new Attendance;
                $attendance->lrn = $lrn;
                $attendance->first_name = $student->first_name;
                $attendance->last_name = $student->last_name;
                $attendance->middle_name = $student->middle_name;
                $attendance->section_id = $student->section_id;
                $attendance->grade_level = $student->grade_level;
                $attendance->scan_timestamp = now();
                $attendance->is_absent = false;
                $attendance->is_late = $is_late;
                $attendance->save();

                return 'Attendance added successfully';
            }

            return 'Student not found';
        } catch (\Error $th) {
            return 'Error: '.$th->getMessage();
        }
    }

    public function getAttendanceRecords(array $dateRange, int|string|null $section = null): \Illuminate\Support\Collection|string
    {
        try {
            $query = Attendance::whereBetween('created_at', $dateRange);
            $user = auth()->user();

            if ($user?->is_admin) {
                if ($section) {
                    $query->where('section_id', $section);
                }
            } elseif ($user?->teacher) {
                if ($user->teacher->section_id) {
                    $query->where('section_id', $user->teacher->section_id);
                } else {
                    return collect();
                }
            } elseif ($section) {
                $query->where('section_id', $section);
            }

            return $query->latest()->get();
        } catch (\Throwable $th) {
            return 'Error: '.$th->getMessage();
        }
    }

    public function updateAttendanceRecord(int $id, array $data): Attendance|string
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->update($data);

            return $attendance;
        } catch (\Exception $e) {
            return 'Error: '.$e->getMessage();
        }
    }

    public function deleteAttendanceRecord(int $id): string
    {
        try {
            $attendance = Attendance::where('id', $id)->first();
            if ($attendance) {
                $attendance->delete();

                return 'Attendance deleted successfully';
            }

            return 'Attendance not found';
        } catch (\Error $th) {
            return 'Error: '.$th->getMessage();
        }
    }
}
