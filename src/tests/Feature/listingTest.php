<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Condition;

class listingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_出品商品情報登録()
    {

        // テスト用ストレージ
        Storage::fake('public');

        // 条件を作る
        $condition = Condition::factory()->create();

        //ユーザー作成ログイン
        $user = User::factory()->create();

        //カテゴリを複数作成
        $categories = Category::factory()->count(2)->create();
        // カテゴリIDをカンマ区切り文字列に変換
        $categoryIdsString = $categories->pluck('id')->implode(',');

        //出品する商品情報
        $itemData = [
            'user_id' => $user->id,
            'name' => '魔法の杖',
            'status' => 'available',
            'condition_id' => $condition->id, // ← ここに作った条件のIDを入れる
            'brand' => 'オリバンダー',
            'description' => '最強の杖です',
            'price' => 5000,
            'item_path' => UploadedFile::fake()->image('test.jpg'),
            'category_id' => $categoryIdsString, // ← カンマ区切り文字列
        ];

        //商品出品画面POST
        $response = $this->actingAs($user)->post(route('items.store'), $itemData);

        //保存後はトップページへ
        $response->assertStatus(302);
        $response->assertRedirect(route('items.index'));

        //DBに保存できているか
        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => '魔法の杖',
            'status' => 'available',
            'condition_id' => $condition->id,
            'brand' => 'オリバンダー',
            'description' => '最強の杖です',
            'price' => 5000,
        ]);

        //作成した商品を取得
        $item = Item::first();

        //中間テーブルにカテゴリが紐付いているか
        $item->categories()->sync($categories->pluck('id'));

        foreach ($categories as $category) {
            $this->assertDatabaseHas('items_categories', [
                'item_id' => $item->id,
                'category_id' => $category->id,
            ]);
        }
        // 画像も保存されているか確認
        Storage::disk('public')->assertExists('item/' . $itemData['item_path']->hashName());
    }
}
