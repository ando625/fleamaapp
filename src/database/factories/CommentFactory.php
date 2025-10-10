<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Comment;
use App\Models\User;
use App\Models\Item;

class CommentFactory extends Factory
{

    protected $model = Comment::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'user_id' => User::factory(), // コメントするユーザーを作る
            'item_id' => Item::factory(), // コメント対象の商品を作る
            'content' => $this->faker->sentence(), // ランダムなコメント内容
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
