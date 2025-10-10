<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Condition;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{

    protected $model = Item::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'user_id' => User::factory(),  // 新しいユーザーも自動生成
            'name' => $this->faker->word,
            'brand' => $this->faker->company,
            'description' => $this->faker->sentence,
            'price' => $this->faker->numberBetween(1000, 50000),
            'condition_id' => Condition::factory(), // これでItem生成時にConditionも自動生成
            'status' => 'available', // デフォルトは available
            'item_path' => 'item/default.jpg', // 適宜
        ];
    }
    //購入済みに
    public function sold()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sold',
        ]);
    }
}
