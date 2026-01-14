<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_login_validation_message_email()
    {
        $response = $this->post('/login', [
            'email' => null,
            'password' => 'password123',
        ]);
        $response ->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_login_validation_message_password()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => null,
        ]);
        $response ->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_login_validation_message_no_user()
    {
        $user = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);
        $response ->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }
}