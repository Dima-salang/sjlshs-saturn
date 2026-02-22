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
        return [
            'lrn' => $this->faker->numerify('###########'),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'middle_name' => $this->faker->lastName,
            'grade_level' => 'Grade 11',
            'section_id' => \App\Models\Section::factory(),
            'is_absent' => false,
            'is_late' => false,
            'scan_timestamp' => now(),
        ];
    }
}
