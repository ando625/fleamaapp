<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;


class CommentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;


    public function test_ログイン済みユーザーはコメントを送信できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $condition = Condition::factory()->create();
        $item = Item::factory()->create(['condition_id' => $condition->id]);

        $response = $this->post(route('items.comment', $item), [
            'content' => 'この商品欲しい'
        ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'この商品欲しい'
        ]);

        $response->assertRedirect();

    }

    public function test_ログイン前のユーザーはコメントを送信できない()
    {
        $condition = Condition::factory()->create();
        $item = Item::factory()->create(['condition_id' => $condition->id]);


        $response = $this->post(route('items.comment', $item), [
            'content' => '送信テスト'
        ]);

        $this->assertDatabaseMissing('comments', [
            'content' => '送信テスト'
        ]);

        $response->assertRedirect(route('login'));

    }

    public function test_コメントが空の場合はバリデーションエラーになる()
    {

        $user = User::factory()->create();
        $this->actingAs($user);

        $condition = Condition::factory()->create();
        $item = Item::factory()->create(['condition_id' => $condition->id]);

        $response = $this->post(route('items.comment', $item), [
            'content' => ''
        ]);

        $response->assertSessionHasErrors('content');
    }


    public function test_コメントが255文字以上の場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $condition = Condition::factory()->create();
        $item = Item::factory()->create(['condition_id' => $condition->id]);
        $longComment = str_repeat('あ', 256);

        $response = $this->post(route('items.comment', $item), [
            'content' => $longComment
        ]);

        $response->assertSessionHasErrors('content');


    }
}

