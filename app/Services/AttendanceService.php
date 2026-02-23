<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Student;

class AttendanceService
{
    public function addAttendanceRecord(array $decoded): string
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
                $attendance->gender = $student->gender;
                $attendance->grade_level = $student->grade_level;
                $attendance->adviser_id = $student->adviser_id;
                $attendance->save();

                return 'Attendance added successfully';
            }

            return 'Student not found';
        } catch (\Error $th) {
            return 'Error: '.$th->getMessage();
        }
    }

    public function getAttendanceRecords(array $dateRange, int|string $section): \Illuminate\Support\Collection|string
    {
        try {
            $attendance = Attendance::whereBetween('created_at', $dateRange)->where('section_id', $section)->get();

            return $attendance;
        } catch (\Error $th) {
            return 'Error: '.$th->getMessage();
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
