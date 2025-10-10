<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;


class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_全商品を取得できる()
    {
        //商品を３つ作成
        $items = Item::factory()->count(3)->create();

        //商品一覧ページにアクセス
        $response = $this->get('/');

        //HTTPステータスが２００（正常）であることを確認
        $response->assertStatus(200);

        //制作した商品名が画面に表示されていることを確認
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    public function test_購入済み商品は「Sold」と表示される()
    {
        //購入済み商品を作成
        $soldItem = Item::factory()->create([
            'status' => 'sold',
        ]);

        //商品一覧ページにアクセス(未ログイン)
        $response = $this->get('/');

        $response->assertStatus(200);
        //sold文字が表示されているか確認
        $response->assertSee('Sold');
        //購入済み商品名も表示されていることを確認
        $response->assertSee($soldItem->name);
    }

    public function test_未認証ユーザーはマイリストページで何も表示されない()
    {
        // 未ログイン状態でマイリストタブにアクセス
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);

        // 「.product-item」がHTMLに存在しないことを確認
        $response->assertDontSee('<div class="product-item">', false);

    }

    public function test_「商品名」で部分一致検索ができる()
    {
        //テスト用ユーザーと商品を作成
        $user = User::factory()->create();
        $matchingItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '魔法の杖',
        ]);
        $nonMatchingItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'ほうき',
        ]);

        //検索キーワードを用意
        $keyword = '杖';

        //getリクエストで検索
        $response = $this->get('/?q=' . $keyword);

        //レスポンスに部分一致する商品が表示されるか
        $response->assertSee($matchingItem->name);
        $response->assertDontSee($nonMatchingItem->name);
    }
}
