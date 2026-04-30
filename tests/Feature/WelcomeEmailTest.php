<?php

namespace Tests\Feature;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_email_contains_correct_subject(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $password = 'SecretPassword123';
        $mail = new WelcomeEmail($user, $password);

        $envelope = $mail->envelope();

        $this->assertEquals('Welcome to TheSyndicate - Your Account is Ready!', $envelope->subject);
    }

    public function test_welcome_email_renders_successfully(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $password = 'TestPassword123';
        $mail = new WelcomeEmail($user, $password);

        $rendered = $mail->render();

        $this->assertStringContainsString('Test User', $rendered);
        $this->assertStringContainsString('test@example.com', $rendered);
        $this->assertStringContainsString($password, $rendered);
    }

    public function test_welcome_email_contains_login_credentials(): void
    {
        $user = User::factory()->create([
            'name' => 'John Smith',
            'email' => 'johnsmith@example.com',
        ]);

        $password = 'MySecurePass123';
        $mail = new WelcomeEmail($user, $password);

        $rendered = $mail->render();

        // Check that credentials are present
        $this->assertStringContainsString('johnsmith@example.com', $rendered);
        $this->assertStringContainsString('MySecurePass123', $rendered);
        $this->assertStringContainsString('Your Login Credentials', $rendered);
    }
}
