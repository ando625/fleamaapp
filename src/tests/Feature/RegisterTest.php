<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_名前が入力されていない場合はバリデーションエラー()
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

    public function test_メールアドレスが入力されていない場合はバリデーションエラー()
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

    public function test_パスワードが入力されていない場合はバリデーションエラー()
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

    public function test_パスワードが7文字以下の場合はバリデーションエラー()
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

    public function test_パスワードと確認用パスワードが一致しない場合はバリデーションエラー()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password_confirmation']);
    }

    public function test_全て正しく入力された場合はユーザー登録される()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'test',
            'email' => 'test@example.com',
        ]);
    }
}
