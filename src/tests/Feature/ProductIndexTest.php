<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;


class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_全商品を取得できる()
    {
        $items = Item::factory()->count(3)->create();

        $response = $this->get('/');
        $response->assertStatus(200);

        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    public function test_購入済み商品は「Sold」と表示される()
    {
        $soldItem = Item::factory()->create([
            'status' => 'sold',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sold');
        $response->assertSee($soldItem->name);
    }

    public function test_未認証ユーザーはマイリストページで何も表示されない()
    {
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertDontSee('<div class="product-item">', false);

    }

    public function test_「商品名」で部分一致検索ができる()
    {
        $user = User::factory()->create();
        $matchingItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '魔法の杖',
        ]);
        $nonMatchingItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'ほうき',
        ]);

        $keyword = '杖';

        $response = $this->get('/?q=' . $keyword);
        $response->assertSee($matchingItem->name);
        $response->assertDontSee($nonMatchingItem->name);
    }
}
