<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory\<\App\Models\LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, '+1 month');

        return [
            'employee_id' => Employee::factory(),
            'type' => fake()->randomElement(['annual', 'sick', 'emergency', 'maternity', 'other']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => fake()->sentence(),
            'handover_to' => fake()->optional()->name(),
            'handover_notes' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['draft', 'pending', 'approved', 'rejected']),
            'approval_dept_head' => false,
            'approval_hrm' => false,
            'approval_gm' => false,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approval_dept_head' => true,
            'approval_hrm' => true,
            'approval_gm' => true,
            'dept_head_approved_at' => now()->subDays(2),
            'hrm_approved_at' => now()->subDay(),
            'gm_approved_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approval_dept_head' => false,
            'approval_hrm' => false,
            'approval_gm' => false,
        ]);
    }
}
