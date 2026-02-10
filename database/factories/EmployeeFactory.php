<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory\<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    private static $counter = 1;

    public function definition(): array
    {
        return [
            'employee_id' => 'EMP-' . str_pad(self::$counter++, 3, '0', STR_PAD_LEFT),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'position_id' => Position::factory(),
            'department_id' => Department::factory(),
            'join_date' => fake()->date(),
            'status' => fake()->randomElement(['active', 'on_leave', 'terminated']),
            'address' => fake()->address(),
            'avatar' => null,
        ];
    }
}
