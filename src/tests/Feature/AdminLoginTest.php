<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_admin_login_message_email()
    {
        $response = $this->from('/admin/login')
                ->post('/login', [
            'email' => null,
            'password' => 'password123',
        ]);
        // $response->assertStatus(302);
        // $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);

    }

    public function test_admin_login_message_password()
    {
        $response = $this->from('/admin/login')->post('/login', [
            'email' => 'admin@admin.com',
            'password' => null,
        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_admin_login_message_no_admin()
    {
        $admin = [
            'email' => 'admin@admin.com',
            'password' => 'password123',
        ];
        $response = $this->from('/admin/login')->post('/login', [
            'email' => 'wrong@wrong.com',
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }
}