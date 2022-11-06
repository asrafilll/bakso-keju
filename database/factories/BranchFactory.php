<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Branch #' . $this->faker->randomNumber(3, true),
            'phone' => '0813 1582 7396',
            'order_number_prefix' => $this->faker->colorName(),
            'next_order_number' => $this->faker->randomNumber(1),
            'purchase_number_prefix' => $this->faker->colorName(),
            'next_purchase_number' => $this->faker->randomNumber(1),
        ];
    }

    public function main()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_main' => true,
            ];
        });
    }
}
