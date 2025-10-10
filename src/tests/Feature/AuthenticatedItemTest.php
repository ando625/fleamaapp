<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Favorite;

class AuthenticatedItemTest extends TestCase
{
    use RefreshDatabase;

    //テスト用ユーザー作成　ログイン
    protected function setUp(): void
    {
        parent::setUp();

        //ユーザー作成
        $this->user = User::factory()->create(); // ← $this->user に保存
        //Fortify対応でログイン
        $this->actingAs($this->user);
    }

    public function test_自分が出品した商品は表示されない()
    {
        //他ユーザーのアイテム
        $otherUserItem = Item::factory()->create([
            'name' => 'other-item-test'
        ]);

        //自分のアイテム
        $myItem = Item::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'my-item-test'
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee($otherUserItem->name); //他人のアイテム表示
        $response->assertDontSee($myItem->name);  //自分のアイテムは表示されない
    }

    public function test_いいねした商品だけが表示される()
    {
        $item1 = Item::factory()->create(['name' => 'グローブ']);
        $item2 = Item::factory()->create(['name' => 'バット']);

        //item1をいいね登録
        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'item_id' => $item1->id,
        ]);

        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee($item1->name);  //いいねしたアイテム表示
        $response->assertDontSee($item2->name); //いいねしてないアイテム非表示

    }

    public function test_購入済みアイテムには「Sold」ラベルが表示される()
    {
        $soldItem = Item::factory()->create(['status' => 'sold']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sold');
        $response->assertSee($soldItem->name);
    }

    public function test_検索状態がマイリストでも保持されている()
    {
        //テスト用のデータ作成
        $itemMatching = Item::factory()->create(['name' => '魔法の杖']);
        $itemNonMatching = Item::factory()->create(['name' => 'ほうき']);

        // 2. ログインユーザーが「魔法の杖」をお気に入りに登録
        \App\Models\Favorite::factory()->create([
            'user_id' => $this->user->id,
            'item_id' => $itemMatching->id,
        ]);
        // ほうきはお気に入り登録しない


        //検索キーワード設定
        $keyword = '魔法';

        //ログイン済みで検索
        $response = $this->get('/?q=' . $keyword);

        //部分一致した表品が表示されているか
        $response->assertSee($itemMatching->name);
        $response->assertDontSee($itemNonMatching->name);

        //マイリストページに移行しても検索が保存されているか
        $response = $this->get('/?tab=mylist&q=' . $keyword);

        //同じように検索条件が保存されている
        $response->assertSee($itemMatching->name);
        $response->assertDontSee($itemNonMatching->name);
    }
}
