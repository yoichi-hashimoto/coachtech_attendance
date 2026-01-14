<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\User;


class MailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_authMail_send(){
        Notification::fake();

        $this->post(route('register',[
            'name' => 'Mike',
            'email' => 'mike123@mike.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]))
            ->assertStatus(302);

        $user = User::where('email','mike123@mike.com')->first();
        $this->assertNotNull($user);

        Notification::assertSentTo($user,VerifyEmail::class);
    }

    public function test_authMail_verification_site(){

        $this->post(route('register',[
            'name' => 'Mike',
            'email' => 'mike123@mike.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]))
            ->assertStatus(302);

        $this->get(asset('http://localhost:8025/'))
            ->assertStatus(200);

    }

    public function test_authMail_verification_page_move(){

        $user = User::factory()->create([
            'role' => 'user',
            'email_verified_at' => now()
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance'));

        $response -> assertStatus(200);

        $response -> assertSee('出勤'); 
    }
}
