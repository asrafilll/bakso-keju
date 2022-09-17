<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Customer #' . $this->faker->randomNumber(3, true),
            'percentage_discount' => $this->faker->randomNumber(2, true),
        ];
    }
}
