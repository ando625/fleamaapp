<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * メールアドレスが入力されていない場合のバリデーションテスト
     *
     * 手順:
     * 1. ログインページを開く
     * 2. メールアドレスを空にし、パスワードを入力
     * 3. ログインボタンを押す
     */
    public function test_メールアドレスが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * パスワードが入力されていない場合のバリデーションテスト
     *
     * 手順:
     * 1. ログインページを開く
     * 2. パスワードを空にし、メールアドレスを入力
     * 3. ログインボタンを押す
     */
    public function test_パスワードが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * 入力情報が間違っている場合の認証エラーテスト
     *
     * 手順:
     * 1. ログインページを開く
     * 2. 登録されているユーザーのメールアドレスを入力する
     * 3. 間違ったパスワードを入力する
     * 4. ログインボタンを押す
     */
    public function test_入力情報が間違っている場合、バリデーションメッセージが表示される()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correctpassword'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');       // ログインページに戻る
        $response->assertSessionHasErrors('email'); // 認証失敗時は email にバリデーションエラー
        $this->assertGuest();               // ログインされていないことを確認
    }

    /**
     * 正しい情報でログインできるテスト
     *
     * 手順:
     * 1. ログインページを開く
     * 2. 正しいメールアドレスとパスワードを入力
     * 3. ログインボタンを押す
     * 4. ダッシュボード画面にリダイレクトされる
     */
    public function test_正しい情報が入力された場合、ログイン処理が実行される()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_ログアウトができる()
    {
        // 1. テスト用ユーザー作成
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 2. ログイン状態にする
        $this->actingAs($user);

        // 3. ログアウト処理を POST で呼ぶ
        $response = $this->post('/logout');

        // 4. ログインしていたユーザーがログアウトされていることを確認
        $this->assertGuest();

        // 5. ログアウト後にリダイレクトされるページを確認
        $response->assertRedirect('/');
    }
}
