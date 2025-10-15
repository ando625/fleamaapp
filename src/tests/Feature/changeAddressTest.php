<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChangeAddressAndPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_配送先変更が購入画面に反映される_and_注文登録される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();
        $address = [
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区神南',
            'building' => 'テストマンション',
        ];

        $this->post(route('purchase.updateAddress', $item->id), $address);


        $response = $this->get(route('purchase.buy', $item->id));
        $response->assertSee('〒 123-4567');
        $response->assertSee('東京都渋谷区神南');
        $response->assertSee('テストマンション');

        $this->post(route('purchase.checkout', $item->id), [
            'payment_method' => 'konbini',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'convenience_store',
            'recipient_postal' => $address['postal_code'],
            'recipient_address' => $address['address'],
            'recipient_building' => $address['building'],
        ]);
    }
}
