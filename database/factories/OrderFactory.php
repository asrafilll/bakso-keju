<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'order_number' => '#' . $this->faker->randomNumber(4, true),
            'reseller_order' => false,
            'customer_name' => $this->faker->lastName(),
            'percentage_discount' => 0,
            'total_discount' => 0,
            'total_line_items' => 0,
            'total_price' => 0,
        ];
    }
}
