<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Condition;

class ShowTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;


    public function test_商品詳細情報取得()
    {
        $user = User::factory()->create(['name' => 'harry']);
        $condition = Condition::factory()->create(['name' => '良好']);
        $category1 = Category::factory()->create(['name' => 'ファッション']);
        $category2 = Category::factory()->create(['name' => 'ゲーム']);

        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'ハリーの杖',
            'brand' => 'オリバンダー製',
            'price' => 12345,
            'description' => '最強の魔法の杖です',
            'condition_id' => $condition->id,
        ]);

        $item->categories()->attach([$category1->id, $category2->id]);

        $commentUser = User::factory()->create(['name' => 'ロン']);
        Comment::factory()->create([
            'user_id' => $commentUser->id,
            'item_id' => $item->id,
            'content' => '何製？'
        ]);

        Favorite::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);

        $response = $this->get(route('items.show', $item));
        $response->assertStatus(200);
        $response->assertSee('ハリーの杖');
        $response->assertSee('オリバンダー製');
        $response->assertSee('12,345');
        $response->assertSee('最強の魔法の杖です');
        $response->assertSee('良好');
        $response->assertSee($category1->name);
        $response->assertSee($category2->name);
        $response->assertSee('ロン');
        $response->assertSee('何製？');
        $this->assertEquals(1, $item->favorite_count);
        $this->assertEquals(1, $item->comment_count);

    }
}
