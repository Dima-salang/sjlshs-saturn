<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $student = \App\Models\Student::factory()->create();

        return [
            'lrn' => $student->lrn,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'middle_name' => $student->middle_name,
            'grade_level' => $student->grade_level,
            'section_id' => $student->section_id,
            'is_absent' => false,
            'is_late' => false,
            'scan_timestamp' => now(),
        ];
    }
}
