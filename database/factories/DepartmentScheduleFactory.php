<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\DepartmentSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentScheduleFactory extends Factory
{
    protected $model = DepartmentSchedule::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'year' => now()->year,
            'month' => now()->month,
            'status' => DepartmentSchedule::STATUS_DRAFT,
            'created_by' => User::factory(),
            'submitted_by' => null,
            'submitted_at' => null,
            'applied_by' => null,
            'applied_at' => null,
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DepartmentSchedule::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public function applied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DepartmentSchedule::STATUS_APPLIED,
            'submitted_at' => now(),
            'applied_at' => now(),
        ]);
    }
}
