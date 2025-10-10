<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\User;
use App\Models\Favorite;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;


    public function test_いいね機能()
    {
        //ユーザー作成
        $user = User::factory()->create();
        $this->actingAs($user);  //ログイン状態にする

        //条件・商品作成
        $condition = Condition::factory()->create();
        $item = Item::factory()->create([
            'condition_id' => $condition->id,
        ]);

        //いいね追加
        $response = $this->post(route('items.favorite', $item));
        $response->assertRedirect();

        //DBに保存できているか
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        //bladeでの合計値確認
        $this->assertEquals(1,$item->fresh()->favorite_count);

        //いいね解除
        $response = $this->delete(route('items.unfavorite', $item));
        $response->assertRedirect();

        //DBから消去されているか
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        //blade上での合計確認
        $this->assertEquals(0, $item->fresh()->favorite_count);

    }
}
