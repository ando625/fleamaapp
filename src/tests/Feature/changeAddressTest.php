<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class changeAddressTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;



    protected $user;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        // ユーザー作成＆ログイン
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // 商品作成（未購入）
        $this->item = Item::factory()->create();
    }

    /** @test */
    public function test_送付先住所変更画面にて登録した住所が商品購入画面に反映されている()
    {
        //送付先住所変更画面にアクセス
        $response = $this->get(route('purchase.change', $this->item->id));
        $response->assertStatus(200);
        // 初期値はプロフィール住所
        $response->assertSee($this->user->profile->postal_code);
        $response->assertSee($this->user->profile->address);
        $response->assertSee($this->user->profile->building);

        //住所を更新（購入時のみ）
        $newAddress = [
            'postal_code' => '987-6543',
            'address' => '東京都千代田区新町',
            'building' => '新ビル202',
        ];

        $this->post(route('purchase.updateAddress', $this->item->id), $newAddress)
            ->assertRedirect(route('purchase.buy', $this->item->id));

        // 3. 購入画面に再度アクセス
        $response = $this->get(route('purchase.buy', $this->item->id));
        $response->assertStatus(200);
        // セッションに保存した新住所が表示される
        $response->assertSee($newAddress['postal_code']);
        $response->assertSee($newAddress['address']);
        $response->assertSee($newAddress['building']);

        // プロフィール住所は変わらない
        $this->assertEquals('123-4567', $this->user->profile->postal_code);
        $this->assertEquals('東京都渋谷区神南', $this->user->profile->address);
        $this->assertEquals('テストマンション', $this->user->profile->building);
    }

    /** @test */
    public function test_購入した商品に送付先住所が紐づいて登録される()
    {
        // 住所を更新
        $newAddress = [
            'postal_code' => '987-6543',
            'address' => '東京都千代田区新町',
            'building' => '新ビル202',
        ];
        $this->post(route('purchase.updateAddress', $this->item->id), $newAddress);

        // 購入実行
        $purchaseData = [
            'payment_method' => 'credit',
        ];
        $this->post(route('purchase.store', $this->item->id), $purchaseData)
            ->assertRedirect('/')
            ->assertSessionHas('success', '購入が完了しました');

        // Orderに購入時住所が保存されている
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'recipient_postal' => $newAddress['postal_code'],
            'recipient_address' => $newAddress['address'],
            'recipient_building' => $newAddress['building'],
            'payment_method' => 'credit',
        ]);

        // 商品ステータスが sold になっている
        $this->assertEquals('sold', $this->item->fresh()->status);
    }
}
