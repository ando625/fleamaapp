<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\User;
use App\Models\Item;

class OrderFactory extends Factory
{
    protected $model = Order::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'recipient_name' => $this->faker->name(),
            'recipient_postal' => $this->faker->postcode(),
            'recipient_address' => $this->faker->address(),
            'recipient_building' => $this->faker->optional()->secondaryAddress(),
            'payment_method' => $this->faker->randomElement(['card', 'convenience_store']),
        ];
    }
}
