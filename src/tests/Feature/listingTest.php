<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
        Storage::fake('public');

        $condition = Condition::factory()->create();
        $user = User::factory()->create();

        $categories = Category::factory()->count(2)->create();
        $categoryIdsString = $categories->pluck('id')->implode(',');

        $itemData = [
            'user_id' => $user->id,
            'name' => '魔法の杖',
            'status' => 'available',
            'condition_id' => $condition->id,
            'brand' => 'オリバンダー',
            'description' => '最強の杖です',
            'price' => 5000,
            'category_id' => $categoryIdsString,
            'item_path' => UploadedFile::fake()->create('test.jpg', 100),
        ];

        $response = $this->actingAs($user)->post(route('items.store'), $itemData);
        $response->assertStatus(302);
        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => '魔法の杖',
            'status' => 'available',
            'condition_id' => $condition->id,
            'brand' => 'オリバンダー',
            'description' => '最強の杖です',
            'price' => 5000,
        ]);

        $item = Item::first();

        $item->categories()->sync($categories->pluck('id'));

        foreach ($categories as $category) {
            $this->assertDatabaseHas('items_categories', [
                'item_id' => $item->id,
                'category_id' => $category->id,
            ]);
        }
    }
}
