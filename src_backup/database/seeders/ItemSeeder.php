<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'name' => '腕時計',
                'price' => '15000',
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'condition_id' => '1',
                'status' => 'available',
                'item_path' => 'item/ArmaniClock.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1,
            ],
            [
                'name' => 'HDD',
                'price' => '5000',
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'condition_id' => '2',
                'status' => 'available',
                'item_path' => 'item/HDD.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1,
            ],
            [
                'name' => '玉ねぎ３束',
                'price' => '300',
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ３束セット',
                'condition_id' => '3',
                'status' => 'available',
                'item_path' => 'item/Onion.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1,
            ],
            [
                'name' => '革靴',
                'price' => '4000',
                'brand' => '',
                'description' => 'クラシックなデザインの革靴',
                'condition_id' => '4',
                'status' => 'available',
                'item_path' => 'item/Shoes.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1,
            ],
            [
                'name' => 'ノートPC',
                'price' => '45000',
                'brand' => '',
                'description' => '高性能なノートパソコン',
                'condition_id' => '1',
                'status' => 'available',
                'item_path' => 'item/Laptop.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1,
            ],
            [
                'name' => 'マイク',
                'price' => '8000',
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'condition_id' => '2',
                'status' => 'available',
                'item_path' => 'item/Mic.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1,
            ],
            [
                'name' => 'ショルダーバック',
                'price' => '3500',
                'brand' => '',
                'description' => 'おしゃれなショルダーバック',
                'condition_id' => '3',
                'status' => 'available',
                'item_path' => 'item/bag.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1,
            ],
            [
                'name' => 'タンブラー',
                'price' => '500',
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'condition_id' => '4',
                'status' => 'available',
                'item_path' => 'item/Tumbler.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1,
            ],
            [
                'name' => 'コーヒーミル',
                'price' => '4000',
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'condition_id' => '1',
                'status' => 'available',
                'item_path' => 'item/CoffeeGrinder.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1,
            ],
            [
                'name' => 'メイクセット',
                'price' => '2500',
                'brand' => '',
                'description' => '便利なメイクアップセット',
                'condition_id' => '2',
                'status' => 'available',
                'item_path' => 'item/Makeupset.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1,
            ],
        ];

        DB::table('items')->insert($items);
    }
}
