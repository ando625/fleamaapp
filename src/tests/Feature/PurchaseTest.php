<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Order;
use App\Models\Profile;

class PurchaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;


    public function test_「購入する」ボタンを押下すると購入が完了するSold表示()
    {
        // 出品者作成（プロフィールは必須なので作る）
        $seller = User::factory()->create();

        // 商品作成（出品者のIDをセット）
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'status'  => 'available',
        ]);

        // 購入者作成＆プロフィール作成
        $buyer = User::factory()->create();

        // 購入者でログイン
        $this->actingAs($buyer);

        // 購入処理を実行（POSTリクエスト）
        $response = $this->post(route('purchase.store', $item->id), [
            'recipient_name'    => '購入者テスト',
            'recipient_postal'  => $buyer->profile->postal_code,
            'recipient_address' => $buyer->profile->address,
            'recipient_building' => $buyer->profile->building,
            'payment_method'    => 'カード支払',
        ]);

        // 購入後リダイレクト確認
        $response->assertRedirect();

        // オーダーがDBに登録されているか確認
        $this->assertDatabaseHas('orders', [
            'user_id'        => $buyer->id,
            'item_id'        => $item->id,
            'recipient_name' => $buyer->name,
            'payment_method' => 'カード支払',
        ]);

        // 商品が「sold」に変更されたか確認
        $this->assertEquals('sold', $item->fresh()->status);

    }

    public function test_購入した商品はプロフィールの購入履歴に表示される()
    {
        // 出品者作成
        $seller = User::factory()->create();

        // 商品作成
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'status'  => 'available',
            'name'    => 'テスト商品',
        ]);

        // 購入者作成＆プロフィール作成
        $buyer = User::factory()->create();
        $this->actingAs($buyer);

        // 購入
        $this->post(route('purchase.store', $item->id), [
            'recipient_name'    => '購入者テスト',
            'recipient_postal'  => $buyer->profile->postal_code,
            'recipient_address' => $buyer->profile->address,
            'recipient_building' => $buyer->profile->building,
            'payment_method'    => 'コンビニ支払',
        ]);

        // マイページ購入履歴タブを開く
        $response = $this->get(route('mypage', ['page' => 'buy']));
        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('sold');
    }
    

    public function test_支払方法が正しく反映される()
    {
        // 出品者作成＆プロフィール
        $seller = User::factory()->create();

        // 商品作成
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'status'  => 'available',
        ]);

        // 購入者作成＆プロフィール作成
        $buyer = User::factory()->create();
        $this->actingAs($buyer);

        // 購入画面にアクセス
        $response = $this->get(route('purchase.buy', $item));
        $response->assertStatus(200);
        $response->assertSee('コンビニ払い');
        $response->assertSee('カード支払');

        // 支払い方法を選択して購入
        $response = $this->post(route('purchase.store', $item->id), [
            'recipient_name'    => '購入者テスト',
            'recipient_postal'  => $buyer->profile->postal_code,
            'recipient_address' => $buyer->profile->address,
            'recipient_building' => $buyer->profile->building,
            'payment_method'    => 'convenience',
        ]);

        $response->assertRedirect();

        // DB に正しく保存されているか確認
        $this->assertDatabaseHas('orders', [
            'item_id'        => $item->id,
            'user_id'        => $buyer->id,
            'payment_method' => 'convenience',
        ]);
    }
}
