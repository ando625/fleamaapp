<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;


    public function test_いいね機能()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $condition = Condition::factory()->create();
        $item = Item::factory()->create([
            'condition_id' => $condition->id,
        ]);

        $response = $this->post(route('items.favorite', $item));
        $response->assertRedirect();

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertEquals(1,$item->fresh()->favorite_count);

        $response = $this->delete(route('items.unfavorite', $item));
        $response->assertRedirect();

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertEquals(0, $item->fresh()->favorite_count);

    }
}
