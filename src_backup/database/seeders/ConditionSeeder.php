<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conditions = ['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'];

        foreach ($conditions as $condition) {
            DB::table('conditions')->insert([
                'name' => $condition,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
