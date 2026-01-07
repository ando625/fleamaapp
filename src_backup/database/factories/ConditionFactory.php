<?php

namespace Database\Factories;

use App\Models\Condition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConditionFactory extends Factory
{
    protected $model = Condition::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
