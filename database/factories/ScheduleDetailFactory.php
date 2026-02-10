<?php

namespace Database\Factories;

use App\Models\DepartmentSchedule;
use App\Models\Employee;
use App\Models\ScheduleDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleDetailFactory extends Factory
{
    protected $model = ScheduleDetail::class;

    public function definition(): array
    {
        return [
            'department_schedule_id' => DepartmentSchedule::factory(),
            'employee_id' => Employee::factory(),
            'date' => now()->toDateString(),
            'shift_id' => \App\Models\Shift::factory(),
        ];
    }
}
