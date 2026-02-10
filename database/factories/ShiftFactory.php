<?php

namespace Database\Factories;

use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Pagi', 'Siang', 'Malam']),
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ];
    }
}
