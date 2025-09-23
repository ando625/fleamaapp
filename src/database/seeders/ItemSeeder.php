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
                'condition' => '良好',
                'status' => 'available',
                'item_path' => 'storage/item/ArmaniClock.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1, //これでuser_idを入れている
                
            ],
            [
                'name' => 'HDD',
                'price' => '5000',
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'condition' => '目立った傷や汚れなし',
                'status' => 'available',
                'item_path' => 'storage/item/HDD.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1, 
            ],
            [
                'name' => '玉ねぎ３束',
                'price' => '300',
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ３束セット',
                'condition' => 'やや傷や汚れあり',
                'status' => 'available',
                'item_path' => 'storage/item/Onion.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1, 
            ],
            [
                'name' => '革靴',
                'price' => '4000',
                'brand' => '',
                'description' => 'クラシックなデザインの革靴',
                'condition' => '状態が悪い',
                'status' => 'available',
                'item_path' => 'storage/item/Shoes.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1, 
            ],
            [
                'name' => 'ノートPC',
                'price' => '45000',
                'brand' => '',
                'description' => '高性能なノートパソコン',
                'condition' => '良好',
                'status' => 'available',
                'item_path' => 'storage/item/Laptop.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1, 
            ],
            [
                'name' => 'マイク',
                'price' => '8000',
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'condition' => '目立った傷や汚れなし',
                'status' => 'available',
                'item_path' => 'storage/item/Mic.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1, 
            ],
            [
                'name' => 'ショルダーバック',
                'price' => '3500',
                'brand' => '',
                'description' => 'おしゃれなショルダーバック',
                'condition' => 'やや傷や汚れあり',
                'status' => 'available',
                'item_path' => 'storage/item/bag.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1, 
            ],
            [
                'name' => 'タンブラー',
                'price' => '500',
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'condition' => '状態が悪い',
                'status' => 'available',
                'item_path' => 'storage/item/Tumbler.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1, // ← ここを追加
            ],
            [
                'name' => 'コーヒーミル',
                'price' => '4000',
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'condition' => '良好',
                'status' => 'available',
                'item_path' => 'storage/item/CoffeeGrinder.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1, 
            ],
            [
                'name' => 'メイクセット',
                'price' => '2500',
                'brand' => '',
                'description' => '便利なメイクアップセット',
                'condition' => '目立った傷や汚れなし',
                'status' => 'available',
                'item_path' => 'storage/item/Makeupset.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => 1, 
            ],
            
        ];

        DB::table('items')->insert($items); // ← ここで実際に登録
    }
}
