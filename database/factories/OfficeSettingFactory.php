<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory\<\App\Models\OfficeSetting>
 */
class OfficeSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Office',
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'radius' => 100,
            'tolerance' => 10,
            'work_start_time' => '09:00',
            'work_end_time' => '17:00',
            'is_active' => true,
        ];
    }
}
