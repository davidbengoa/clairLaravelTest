<?php

namespace Database\Factories;

use App\Models\Business;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Business>
 */
class BusinessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws Exception
     */
    public function definition()
    {
        return [
            'name' => fake()->company(),
            'external_id' => Str::random(10),
            'enabled' => (bool)random_int(0,1),
            'deduction' => random_int(1,100),
            'email' => fake()->safeEmail(),
        ];
    }
}
