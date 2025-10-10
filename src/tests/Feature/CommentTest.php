<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        //ユーザー作成ログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        //商品・条件
        $condition = Condition::factory()->create();
        $item = Item::factory()->create(['condition_id' => $condition->id]);

        //コメント送信
        $response = $this->post(route('items.comment', $item), [
            'content' => 'この商品欲しい'
        ]);

        //DBに保存確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'この商品欲しい'
        ]);

        //コメント送信後商品一覧にいるかリダイレクト
        $response->assertRedirect();

    }

    public function test_ログイン前のユーザーはコメントを送信できない()
    {
        $condition = Condition::factory()->create();
        $item = Item::factory()->create(['condition_id' => $condition->id]);

        // 未ログインでコメント送信
        $response = $this->post(route('items.comment', $item), [
            'content' => '送信テスト'
        ]);

        // コメントは保存されないかどうか
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

        //コメントを送信
        $response = $this->post(route('items.comment', $item), [
            'content' => ''
        ]);

        //バリデーションエラー
        $response->assertSessionHasErrors('content');
    }


    public function test_コメントが255文字以上の場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $condition = Condition::factory()->create();
        $item = Item::factory()->create(['condition_id' => $condition->id]);

        //２５６の繰り返し文字を作成
        $longComment = str_repeat('あ', 256);

        //コメント送信
        $response = $this->post(route('items.comment', $item), [
            'content' => $longComment
        ]);

        //バリデーションエラーの有無確認
        $response->assertSessionHasErrors('content');


    }
}

