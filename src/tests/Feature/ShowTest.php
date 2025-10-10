<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        // テスト用データ作成
        $user = User::factory()->create(['name' => 'harry']);
        $condition = Condition::factory()->create(['name' => '良好']);
        $category1 = Category::factory()->create(['name' => 'ファッション']);
        $category2 = Category::factory()->create(['name' => 'ゲーム']);

        // 商品作成
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'ハリーの杖',
            'brand' => 'オリバンダー製',
            'price' => 12345,
            'description' => '最強の魔法の杖です',
            'condition_id' => $condition->id,
        ]);

        // カテゴリを関連付け
        $item->categories()->attach([$category1->id, $category2->id]);

        // コメント作成
        $commentUser = User::factory()->create(['name' => 'ロン']);
        Comment::factory()->create([
            'user_id' => $commentUser->id,
            'item_id' => $item->id,
            'content' => '何製？'
        ]);

        // いいね作成
        Favorite::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);


        // 商品ページにアクセス
        $response = $this->get(route('items.show', $item));
        $response->assertStatus(200);


        // Blade上の表示確認
        $response->assertSee('ハリーの杖');
        $response->assertSee('オリバンダー製');
        $response->assertSee('12,345');
        $response->assertSee('最強の魔法の杖です');
        $response->assertSee('良好');

        // 複数カテゴリ表示
        $response->assertSee($category1->name);
        $response->assertSee($category2->name);

        // コメント表示確認
        $response->assertSee('ロン');
        $response->assertSee('何製？');


        // いいね数・コメント数確認（アクセサ経由）
        $this->assertEquals(1, $item->favorite_count);
        $this->assertEquals(1, $item->comment_count);

    }
}
