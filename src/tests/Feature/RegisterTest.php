<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_register_validation_message_name()
    {
        $response = $this->post('/register', [
            'name' => null,
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response ->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    public function test_register_validation_message_email()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => null,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response ->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_register_validation_message_password()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ]);
        $response ->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    public function test_register_validation_message_password_confirmation()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);
        $response ->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
    }

    public function test_register_validation_message_none_password()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);
        $response ->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_register_validation_success()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response ->assertSessionHasNoErrors();
    }
}