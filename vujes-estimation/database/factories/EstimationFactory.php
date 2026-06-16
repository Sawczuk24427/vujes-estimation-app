<?php

namespace Database\Factories;

use App\Models\Estimation;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Estimation>
 */
class EstimationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['fixed', 'hourly']);

        $hours = null;
        $hourly_rate = null;
        $price = 0;

        if('type' === 'hourly'){
            $hours = fake()->numberBetween(10,100);
            $hourly_rate = fake()->numberBetween(31, 250);
            $price = $hours * $hourly_rate;
        } else {
            $price = fake()->numberBetween(1000, 100000);
        }




        return [
            'project_id'=>Project::factory(),
            'title'=>fake()->sentence(3),
            'type'=> $type,
            'hours' => $hours,
            'hourly-rate'=> $hourly_rate,
            'price'=> $price

        ];
    }
}
