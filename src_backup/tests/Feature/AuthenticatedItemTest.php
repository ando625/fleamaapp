<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Favorite;

class AuthenticatedItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_自分が出品した商品は表示されない()
    {
        $otherUserItem = Item::factory()->create([
            'name' => 'other-item-test'
        ]);

        $myItem = Item::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'my-item-test'
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee($otherUserItem->name);
        $response->assertDontSee($myItem->name);
    }

    public function test_いいねした商品だけが表示される()
    {
        $item1 = Item::factory()->create(['name' => 'グローブ']);
        $item2 = Item::factory()->create(['name' => 'バット']);

        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'item_id' => $item1->id,
        ]);

        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee($item1->name);
        $response->assertDontSee($item2->name);

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
        $itemMatching = Item::factory()->create(['name' => '魔法の杖']);
        $itemNonMatching = Item::factory()->create(['name' => 'ほうき']);

        \App\Models\Favorite::factory()->create([
            'user_id' => $this->user->id,
            'item_id' => $itemMatching->id,
        ]);

        $keyword = '魔法';
        $response = $this->get('/?q=' . $keyword);
        $response->assertSee($itemMatching->name);
        $response->assertDontSee($itemNonMatching->name);
        $response = $this->get('/?tab=mylist&q=' . $keyword);
        $response->assertSee($itemMatching->name);
        $response->assertDontSee($itemNonMatching->name);
    }
}
