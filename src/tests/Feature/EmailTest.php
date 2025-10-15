<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class EmailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_会員登録後にメール認証通知が送信される()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);

        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
    }


    public function test_認証誘導画面にアクセスできる()
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertStatus(200);
        $response->assertSee('登録していただいたメールアドレスに認証メールを送付しました');
        $response->assertSee('認証はこちらから');
    }


    public function test_認証メールのリンクを押すとプロフィール設定画面に遷移する()
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('profile.create'));

        $this->assertNotNull($user->fresh()->email_verified_at);
    }


}
