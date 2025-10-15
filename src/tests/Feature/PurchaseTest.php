<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;


class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品購入機能()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'status' => 'available',
            'name' => '魔法の杖',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('purchase.checkout', $item->id), [
            'payment_method' => 'konbini',
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'convenience_store',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
        ]);

        $itemsResponse = $this->get(route('items.index'));
        $itemsResponse->assertSee('魔法の杖');
        $itemsResponse->assertSee('sold');

        $profileResponse = $this->get(route('mypage') . '?page=buy');
        $profileResponse->assertSee('魔法の杖');
        $profileResponse->assertSee('sold');
    }


    public function test_小計画面で変更が反映される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create(['status' => 'available', 'name' => '魔法の杖']);

        $this->withSession(['payment_method' => 'konbini']);
        $response = $this->get(route('purchase.buy', $item->id));
        $response->assertSee('value="konbini"', false);
    }
}
