<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Survey>
 */
class SurveyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => rand(1, 10),
            'title' => fake()->title(),
            'image' => fake()->imageUrl(),
            'status' => 1,
            'description' => fake()->text(),
            'expire_date' => fake()->dateTimeBetween(Carbon::now(), Carbon::now()->addYears(6))
        ];
    }
}
