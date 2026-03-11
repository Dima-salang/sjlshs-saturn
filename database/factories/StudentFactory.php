<?php

namespace Database\Factories;

use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lrn' => $this->faker->unique()->numerify('###########'),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'middle_name' => $this->faker->lastName,
            'section_id' => Section::factory(),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'grade_level' => 'Grade 11',
            'adviser_id' => null,
        ];
    }
}
