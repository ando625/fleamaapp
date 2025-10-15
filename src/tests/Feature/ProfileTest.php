<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;


class ProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;


    public function test_ユーザー情報取得()
    {
        $user = User::factory()->create([
            'name' => 'テスト太郎',
        ]);

        $itemsForSale = Item::factory()->count(2)->create([
            'user_id' => $user->id,
            'name' => 'テスト商品'
        ]);

        $seller = User::factory()->create();
        $buyItems = Item::factory()->count(2)->create([
            'user_id' => $seller->id,
            'name' => 'テスト商品'
        ]);
        foreach($buyItems as $item) {
            Order::factory()->create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'payment_method' => 'card',
                'recipient_name' => $user->name,
                'recipient_postal' => $user->profile->postal_code,
                'recipient_address' => $user->profile->address,
                'recipient_building' => $user->profile->building,
            ]);
        }

        $response = $this->actingAs($user)->get('/mypage');

        $response->assertStatus(200);
        $response->assertSeeText($user->name);
        if ($user->profile->profile_image) {
            $response->assertSee($user->profile->profile_image);
        }

        foreach ($itemsForSale as $item) {
            $response->assertSee($item->name);
        }

        foreach ($buyItems as $item) {
            $response->assertSee($item->name);
        }

    }


    public function test_ユーザー情報変更()
    {
        $user = User::factory()->create([
            'name' => '変更テスト',
        ]);

        $user->profile()->update([
            'profile_image' => 'storage/edit_avatar.png',
            'postal_code' => '987-6543',
            'address' => '東京都変更区1-2-3',
            'building' => 'テストハウス'
        ]);

        $response = $this->actingAs($user)->get('/profile/edit');

        $response->assertStatus(200);
        $response->assertSee('value="' . $user->name . '"', false);
        if ($user->profile->profile_image) {
            $response->assertSee($user->profile->profile_image);
        }
        $response->assertSee('value="' . $user->profile->postal_code. "", false);
        $response->assertSee('value="' . $user->profile->address. "", false);
        $response->assertSee('value="' . $user->profile->building . '"', false);


    }
}
