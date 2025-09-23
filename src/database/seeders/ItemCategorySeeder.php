<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('items_categories')->insert([
            ['item_id' => 1, 'category_id' => 1], // 腕時計 → ファッション
            ['item_id' => 1, 'category_id' => 5], // 腕時計 → メンズ
            ['item_id' => 2, 'category_id' => 2], // HDD → 家電
            ['item_id' => 3, 'category_id' => 10], // 玉ねぎ → キッチン
            ['item_id' => 4, 'category_id' => 1], // 革靴 → ファッション
            ['item_id' => 4, 'category_id' => 5], // 革靴 → メンズ
            ['item_id' => 5, 'category_id' => 2], // ノートPC → 家電
            ['item_id' => 6, 'category_id' => 2], // マイク → 家電
            ['item_id' => 7, 'category_id' => 1], // ショルダーバッグ → ファッション
            ['item_id' => 7, 'category_id' => 4], // ショルダーバッグ → レディース
            ['item_id' => 8, 'category_id' => 10], // タンブラー → キッチン
            ['item_id' => 9, 'category_id' => 2], // コーヒーミル → 家電
            ['item_id' => 9, 'category_id' => 1], // コーヒーミル → ファッション（例）
            ['item_id' => 10, 'category_id' => 6], // メイクセット → コスメ
        ]);
        
    }
}
