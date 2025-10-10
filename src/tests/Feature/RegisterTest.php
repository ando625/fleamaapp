<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 名前が入力されていない場合はバリデーションエラー()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function メールアドレスが入力されていない場合はバリデーションエラー()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'test',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function パスワードが入力されていない場合はバリデーションエラー()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function パスワードが7文字以下の場合はバリデーションエラー()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function パスワードと確認用パスワードが一致しない場合はバリデーションエラー()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertRedirect('/register');
        // RegisterRequest では password_confirmation にエラーが入る
        $response->assertSessionHasErrors(['password_confirmation']);
    }

    /** @test */
    public function 全て正しく入力された場合はユーザー登録されプロフィール作成画面にリダイレクト()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('profile.create'));

        $this->assertDatabaseHas('users', [
            'name' => 'test',
            'email' => 'test@example.com',
        ]);
    }
}
