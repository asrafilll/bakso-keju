<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'purchase_number' => '#' . $this->faker->randomNumber(4, true),
            'customer_name' => $this->faker->lastName(),
            'total_line_items_quantity' => 0,
            'total_line_items_price' => 0,
            'total_price' => 0,
        ];
    }
}
