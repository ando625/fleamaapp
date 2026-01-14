<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'ユーザー1',
                'email' => 'test1@example.com',
            ],
            [
                'name' => 'ユーザー2',
                'email' => 'test2@example.com',
            ],
            [
                'name' => 'ユーザー3',
                'email' => 'test3@example.com',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('pass1234'),
                'email_verified_at' => now(),
            ]);

            Profile::create([
                'user_id' => $user->id,
                'postal_code' => '123-4567',
                'address' => '東京都渋谷区',
                'building' => '明マンション123',
            ]);
        }
    }
}
