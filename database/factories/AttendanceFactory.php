<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory\<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'date' => fake()->date(),
            'time_in' => fake()->time('H:i:s'),
            'time_out' => fake()->optional()->time('H:i:s'),
            'status' => fake()->randomElement(['present', 'late', 'absent']),
            'location' => fake()->randomElement(['Kantor Pusat', 'Remote', 'Kantor Cabang']),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'photo' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
